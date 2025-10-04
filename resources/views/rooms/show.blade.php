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
                        {{ $user->name }}
                        @if($user->id !== auth()->id())
                            <button 
                                type="button"
                                onclick="openReportModal({{ $user->id }}, '{{ $user->name }}')" 
                                class="report-btn">
                                Report
                            </button>
                        @endif
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
            <textarea name="message" class="report-textarea" rows="3" placeholder="Reason for report..." required></textarea>
            <div class="report-modal-actions">
                <button type="button" onclick="closeReportModal()" class="btn-cancel">Cancel</button>
                <button type="submit" class="btn-submit">Submit</button>
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

    function appendMessage(user, message) {
        const d = document.createElement('div');
        d.className = (user === currentUserName) ? 'msg sent' : 'msg recv';
        d.innerHTML = `<strong>${escapeHtml(user)}:</strong> ${escapeHtml(message)}`;
        box.appendChild(d);
        box.scrollTop = box.scrollHeight;
    }

    function escapeHtml(s){ return String(s).replace(/[&<>"']/g, function(m){ return ({'&':'&amp;','<':'&lt;','>':'&gt;','"':'&quot;',"'":'&#39;'})[m]; }); }

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
})();

function openReportModal(offenderId, userName) {
    document.getElementById('reportModal').style.display = 'flex';
    document.getElementById('reportUserName').innerText = userName;
    document.getElementById('reportForm').action = `/report/${offenderId}`;
}

function closeReportModal() {
    document.getElementById('reportModal').style.display = 'none';
}
</script>

</body>
</html>