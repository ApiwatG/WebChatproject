<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $room->name }} - Chat Room</title>
    <link rel="stylesheet" href="{{ asset('css/inroom.css') }}">
    
</head>
<body>

<div class="container-wrapper">
    <div class="wrap">
        <div class="scene">
            <div class="minilogo">
                <img src="{{ asset('img/logo.png') }}" alt="Logo">
            </div>
        </div>

        <div class="chatPanel">
            <form method="POST" action="{{ route('rooms.leave', $room->id) }}" style="display:inline;">
                @csrf
                <button type="submit" class="btn-exit">Exit Room</button>
            </form>

            <div class="chatHeader">
                <div>
                    <h2>{{ $room->name }}</h2>
                    <div class="uid">Users: {{ $room->users->count() }}/{{ $room->max_users }}</div>
                </div>
            </div>

            <div id="report-mode-banner" class="report-mode-banner">
                📝 Click on a message from <span id="reportingUserName"></span> to report it
                <button type="button" onclick="cancelReportMode()">Cancel</button>
                <button type="button" onclick="continueWithoutMessage()" style="background: #fbbf24; color: #78350f;">Report without message</button>
            </div>

            <div id="chat-box" class="messages"></div>

            <form id="chat-form" class="inputRow">
                @csrf
                <input type="text" id="message" name="message" placeholder="Type a message..." autocomplete="off">
                <button type="submit">Send</button>
            </form>
        </div>

        <div class="participants-section">
            <h3 class="participants-title">Participants ({{ $room->users->count() }})</h3>
            <ul class="participants-list">
                @foreach($room->users as $user)
                    <li class="participant-item" data-user-id="{{ $user->id }}">
                        <div class="participant-avatar">
                            <x-character-preview 
                                :user="$user" 
                                size="small"
                            />
                        </div>
                        <div class="participant-info">
                            <div class="participant-name">{{ $user->name }}</div>
                            @if($user->id !== auth()->id())
                                <button 
                                    type="button"
                                    id="report-btn-{{ $user->id }}"
                                    onclick="startReportMode({{ $user->id }}, '{{ $user->name }}')" 
                                    class="report-btn">
                                    Report
                                </button>
                            @endif
                        </div>
                    </li>
                @endforeach
            </ul>
        </div>
    </div>
</div>

<div id="reportModal" class="report-modal">
    <div class="report-modal-content">
        <h2 class="report-modal-title">Report <span id="reportUserName"></span></h2>
        <form id="reportForm" method="POST" action="">
            @csrf
            <input type="hidden" name="reported_message" id="reportedMessage">
            
            <div id="selectedMessagePreview" class="selected-message-display" style="display: none;">
                <strong>Reported Message:</strong>
                <div id="selectedMessageContent"></div>
            </div>
            
            <textarea name="message" class="report-textarea" rows="3" placeholder="Describe the issue..." required></textarea>
            <div class="report-modal-actions">
                <button type="button" onclick="closeReportModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Submit Report</button>
            </div>
        </form>
    </div>
</div>

<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>

<script>
(function(){
    if (window.chatInit) return;
    window.chatInit = true;

    const roomId = "{{ $room->id }}";
    const currentUserName = @json(optional(auth()->user())->name);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    const box = document.getElementById('chat-box');
    const form = document.getElementById('chat-form');
    const input = document.getElementById('message');
    
    let reportingUserId = null;
    let reportingUserName = null;
    let selectedMessage = null;
    let isReportMode = false;

    function appendMessage(user, message) {
        const d = document.createElement('div');
        d.className = (user === currentUserName) ? 'msg sent' : 'msg recv';
        d.innerHTML = `<strong>${escapeHtml(user)}:</strong> ${escapeHtml(message)}`;
        d.dataset.user = user;
        d.dataset.message = message;
        
        box.appendChild(d);
        box.scrollTop = box.scrollHeight;
    }

    function selectMessageForReport(messageElement, user) {
        if (!isReportMode || user !== reportingUserName) return;
    
     
        document.querySelectorAll('.msg.selected-for-report').forEach(el => {
            el.classList.remove('selected-for-report');
        });
        
     
        messageElement.classList.add('selected-for-report');
        selectedMessage = {
            user: messageElement.dataset.user,
            message: messageElement.dataset.message
        };
        
   
        openReportModal(reportingUserId, reportingUserName, selectedMessage.message);
    }

    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]; }); }

    box.addEventListener('click', function(e) {
        const msgElement = e.target.closest('.msg.recv.selectable');
        if (msgElement && isReportMode) {
            selectMessageForReport(msgElement, msgElement.dataset.user);
        }
    });

    fetch(`/chat/${roomId}/messages`, { headers: { 'X-CSRF-TOKEN': csrfToken } })
        .then(r => r.ok ? r.json() : Promise.reject(r))
        .then(data => {
            if (!Array.isArray(data)) return;
            data.forEach(m => appendMessage(m.user ?? m.name ?? 'Unknown', m.message ?? m.body ?? ''));
        })
        .catch(err => console.debug('No cached messages or failed to load:', err));

    if (window.Echo && typeof window.Echo.channel === 'function') {
        try {
            window.Echo.channel(`chat.${roomId}`)
                .listen('MessageSent', e => {
                    appendMessage(e.user ?? e.name ?? 'Unknown', e.message ?? e.body ?? '');
                });
        } catch (e) {
            console.warn('Echo subscription failed', e);
        }
    }

    if (form) {
        form.addEventListener('submit', function(ev){
            ev.preventDefault();
            const message = input.value?.trim();
            if (!message) return;
            appendMessage(currentUserName || 'You', message);

            fetch(`/chat/${roomId}/send`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ message })
            }).then(res => {
                if (!res.ok) {
                    console.error('Send failed', res);
                } else {
                    input.value = '';
                }
            }).catch(err => {
                console.error('Failed to send message', err);
            });
        });
    }

    window.startReportMode = function(offenderId, userName) {
        isReportMode = true;
        reportingUserId = offenderId;
        reportingUserName = userName;
        selectedMessage = null;
        
     
        document.getElementById('report-mode-banner').classList.add('active');
        document.getElementById('reportingUserName').textContent = userName;
        
        document.getElementById(`report-btn-${offenderId}`).classList.add('active');
        
    
        document.querySelectorAll('.msg.recv').forEach(msg => {
            if (msg.dataset.user === userName) {
                msg.classList.add('selectable');
            }
        });
    };

    window.cancelReportMode = function() {
        isReportMode = false;
        reportingUserId = null;
        reportingUserName = null;
        selectedMessage = null;
        
        document.getElementById('report-mode-banner').classList.remove('active');
        
    
        document.querySelectorAll('.msg.selectable, .msg.selected-for-report').forEach(el => {
            el.classList.remove('selectable', 'selected-for-report');
        });
        
      
        document.querySelectorAll('.report-btn.active').forEach(btn => {
            btn.classList.remove('active');
        });
    };

    window.continueWithoutMessage = function() {
        openReportModal(reportingUserId, reportingUserName, null);
    };

    window.openReportModal = function(offenderId, userName, message = null) {
        document.getElementById('reportModal').style.display = 'flex';
        document.getElementById('reportUserName').innerText = userName;
        document.getElementById('reportForm').action = `/report/${offenderId}`;
        
        if (message) {
            document.getElementById('reportedMessage').value = message;
            document.getElementById('selectedMessageContent').textContent = `"${message}"`;
            document.getElementById('selectedMessagePreview').style.display = 'block';
        } else {
            document.getElementById('reportedMessage').value = '';
            document.getElementById('selectedMessagePreview').style.display = 'none';
        }
    };

    window.closeReportModal = function() {
        document.getElementById('reportModal').style.display = 'none';
        cancelReportMode();
    };
})();
</script>

</body>
</html>