@extends('layouts.app')

@section('content')

{{-- Include the CSS file --}}
<link rel="stylesheet" href="{{ asset('css/inroom.css') }}">

<div class="wrap">
    {{-- Left scene --}}
    <div class="scene">
        {{-- Example placeholder for your game/room scene --}}
        <div class="minilogo">
            <img src="{{ asset('img/logo.png') }}" alt="Logo">
        </div>
    </div>

    {{-- Right chat panel --}}
    <div class="chatPanel">
        <div class="chatHeader">
            <div>
                <h2 class="text-lg font-bold">{{ $room->name }}</h2>
                <div class="uid">Users: {{ $room->users->count() }}/{{ $room->max_users }}</div>
            </div>
        </div>

        {{-- Messages --}}
        <div id="chat-box" class="messages">
            {{-- Cached + realtime messages will appear here --}}
        </div>

        {{-- Input row --}}
        <form id="chat-form" class="inputRow">
            @csrf
            <input type="text" id="message" name="message" placeholder="Type a message..." autocomplete="off">
            <button type="submit">Send</button>
        </form>
    </div>
</div>

{{-- Participants + Report --}}
<div class="participants-section">
    <h3 class="participants-title">Participants</h3>
    <ul class="participants-list">
        @foreach($room->users as $user)
            <li class="participant-item">
                {{ $user->name }}
                @if($user->id !== auth()->id())
                    <button 
                        type="button"
                        onclick="openReportModal({{ $user->id }}, {!! json_encode($user->name) !!})" 
                        class="report-btn">
                        Report
                    </button>
                @endif
            </li>
        @endforeach
    </ul>
</div>

{{-- Report Modal --}}
<div id="reportModal" class="report-modal" style="display:none;">
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

{{-- Pusher + Echo client (only add if you haven't initialized Echo in your app bundle) --}}
<script src="https://js.pusher.com/7.2/pusher.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/laravel-echo@1.11.0/dist/echo.iife.js"></script>

<script>
    const roomId = "{{ $room->id }}";
    const currentUserName = @json(auth()->user()->name);
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

    // Initialize Echo if it's not already created by your compiled assets
    if (typeof window.Echo === 'undefined') {
        window.Pusher = Pusher;
        window.Echo = new Echo({
            broadcaster: 'pusher',
            key: "{{ config('broadcasting.connections.pusher.key') ?? env('PUSHER_APP_KEY') }}",
            cluster: "{{ config('broadcasting.connections.pusher.options.cluster') ?? env('PUSHER_APP_CLUSTER') }}",
            forceTLS: true,
            auth: {
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }
        });
        console.debug('Echo initialized on page');
    }

    // Load cached messages
    fetch(`/chat/${roomId}/messages`)
        .then(res => res.json())
        .then(data => {
            const box = document.getElementById("chat-box");
            data.forEach(msg => {
                const div = document.createElement("div");
                div.className = (msg.user === currentUserName) ? "msg sent" : "msg recv";
                div.innerHTML = `<strong>${msg.user}:</strong> ${msg.message}`;
                box.appendChild(div);
            });
            box.scrollTop = box.scrollHeight;
        }).catch(err => console.error('Failed to load messages', err));

    // Echo realtime listener
    (function subscribeEcho(retries = 0, maxRetries = 40, delay = 200) {
        if (typeof window.Echo !== 'undefined' && window.Echo && typeof window.Echo.channel === 'function') {
            window.Echo.channel(`chat.${roomId}`)
                .listen("MessageSent", (e) => {
                    const box = document.getElementById("chat-box");
                    const div = document.createElement("div");
                    div.className = (e.user === currentUserName) ? "msg sent" : "msg recv";
                    div.innerHTML = `<strong>${e.user}:</strong> ${e.message}`;
                    box.appendChild(div);
                    box.scrollTop = box.scrollHeight;
                });
        } else if (retries < maxRetries) {
            setTimeout(() => subscribeEcho(retries + 1, maxRetries, delay), delay);
        } else {
            console.warn('Laravel Echo not available after retries; realtime messages will not be received.');
        }
    })();

    // Send message
    document.getElementById("chat-form").addEventListener("submit", function(e){
        e.preventDefault();
        const messageInput = document.getElementById("message");
        const message = messageInput.value.trim();
        if(message === "") return;

        // Optimistic UI: append locally immediately (optional)
        const box = document.getElementById("chat-box");
        const div = document.createElement("div");
        div.className = "msg sent";
        div.innerHTML = `<strong>${currentUserName}:</strong> ${message}`;
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;

        fetch(`/chat/${roomId}/send`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": csrfToken
            },
            body: JSON.stringify({message})
        }).then(res => {
            if (!res.ok) throw new Error('Network response was not ok');
            messageInput.value = "";
        }).catch(err => {
            console.error('Failed to send message', err);
            // optionally show error or remove optimistic message
        });
    });

    // Report modal
    function openReportModal(offenderId, userName) {
        document.getElementById('reportModal').style.display = 'block';
        document.getElementById('reportUserName').innerText = userName;
        document.getElementById('reportForm').action = `/report/${offenderId}`;
    }

    function closeReportModal() {
        document.getElementById('reportModal').style.display = 'none';
    }
</script>

@endsection