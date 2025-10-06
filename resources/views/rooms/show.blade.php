<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $room->name }} - Chat Room</title>
    <link rel="stylesheet" href="{{ asset('css/inroom.css') }}">
    <style>
        .participants-list {
            list-style: none;
            padding: 0;
            margin: 0;
        }

        .participant-item {
            display: flex;
            align-items: center;
            gap: 15px;
            padding: 10px;
            margin-bottom: 10px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 8px;
            transition: background 0.2s;
        }

        .participant-item:hover {
            background: rgba(255, 255, 255, 0.1);
        }

        .participant-avatar {
            flex-shrink: 0;
        }

        .participant-info {
            flex: 1;
            min-width: 0;
        }

        .participant-name {
            font-weight: 600;
            color: #fff;
            margin-bottom: 4px;
        }

        .report-btn {
            padding: 4px 12px;
            background: #ef4444;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 12px;
            cursor: pointer;
            transition: background 0.2s;
        }

        .report-btn:hover {
            background: #dc2626;
        }

        .report-btn.active {
            background: #dc2626;
            animation: pulse 1s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.7; }
        }

        /* Make character preview smaller for participant list */
        .participant-avatar .character-preview-small {
            width: 80px;
        }

        .participant-avatar .character-display-small {
            height: 100px;
        }

        .participant-avatar .character-base-small {
            height: 90%;
        }

        /* Message selection for reporting */
        .msg.recv {
            cursor: default;
            position: relative;
        }

        .msg.recv.selectable {
            cursor: pointer;
            border-left: 3px solid transparent;
        }

        .msg.recv.selectable:hover {
            background: rgba(239, 68, 68, 0.1);
            border-left: 3px solid #ef4444;
        }

        .msg.recv.selected-for-report {
            background: rgba(239, 68, 68, 0.2);
            border-left: 3px solid #ef4444;
        }

        .report-mode-banner {
            padding: 12px;
            background: linear-gradient(90deg, #ef4444, #dc2626);
            color: white;
            text-align: center;
            font-weight: 600;
            display: none;
            margin-bottom: 10px;
            border-radius: 8px;
            animation: slideDown 0.3s ease;
        }

        .report-mode-banner.active {
            display: block;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .report-mode-banner button {
            margin-left: 10px;
            padding: 4px 12px;
            background: white;
            color: #ef4444;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-weight: 600;
        }

        .report-mode-banner button:hover {
            background: #fee;
        }

        .selected-message-display {
            background: rgba(255, 255, 255, 0.05);
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 10px;
            font-size: 13px;
            color: #999;
        }

        .selected-message-display strong {
            color: #fff;
        }
    </style>
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
        
        // Remove previous selection
        document.querySelectorAll('.msg.selected-for-report').forEach(el => {
            el.classList.remove('selected-for-report');
        });
        
        // Select new message
        messageElement.classList.add('selected-for-report');
        selectedMessage = {
            user: messageElement.dataset.user,
            message: messageElement.dataset.message
        };
        
        // Open modal with selected message
        openReportModal(reportingUserId, reportingUserName, selectedMessage.message);
    }

    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]; }); }

    // Click handler for messages
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
        
        // Show banner
        document.getElementById('report-mode-banner').classList.add('active');
        document.getElementById('reportingUserName').textContent = userName;
        
        // Highlight report button
        document.getElementById(`report-btn-${offenderId}`).classList.add('active');
        
        // Make messages from this user selectable
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
        
        // Hide banner
        document.getElementById('report-mode-banner').classList.remove('active');
        
        // Remove selections and highlighting
        document.querySelectorAll('.msg.selectable, .msg.selected-for-report').forEach(el => {
            el.classList.remove('selectable', 'selected-for-report');
        });
        
        // Remove button highlight
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