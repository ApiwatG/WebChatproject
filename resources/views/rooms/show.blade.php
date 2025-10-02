@extends('layouts.app')

@section('content')
<h1 class="text-2xl font-bold mb-4">Room: {{ $room->name }}</h1>

<h3 class="mb-2">Users ({{ $room->users()->count() }}/{{ $room->max_users }}):</h3>
<ul class="mb-6">
    @foreach($room->users as $user)
        <li>{{ $user->name }}</li>
    @endforeach
</ul>

{{-- Chat Section --}}
<div class="border rounded-lg p-4 mb-4">
    <h2 class="text-xl font-semibold mb-3">Chat</h2>

    <div id="chat-box" class="border p-3 h-64 overflow-y-scroll bg-gray-50 mb-3">
        {{-- messages will load here --}}
    </div>

    <form id="chat-form" class="flex gap-2">
        @csrf
        <input type="text" id="message" class="border p-2 flex-grow rounded" placeholder="Type message...">
        <button type="submit" class="bg-blue-500 text-white px-4 rounded">Send</button>
    </form>
</div>

<script>
    const roomId = "{{ $room->id }}";

    // Load cached messages
    fetch(`/chat/${roomId}/messages`)
        .then(res => res.json())
        .then(data => {
            let box = document.getElementById("chat-box");
            data.forEach(msg => {
                box.innerHTML += `<p><b>${msg.user}</b>: ${msg.message}</p>`;
            });
            box.scrollTop = box.scrollHeight;
        });

    // Listen for new messages
    if (window.Echo) {
        window.Echo.join(`chat.${roomId}`)
            .listen("MessageSent", (e) => {
                let box = document.getElementById("chat-box");
                box.innerHTML += `<p><b>${e.user}</b>: ${e.message}</p>`;
                box.scrollTop = box.scrollHeight;
            });
    } else {
        console.error("Echo not initialized. Check bootstrap.js and vite build.");
    }

    // Send message
    document.getElementById("chat-form").addEventListener("submit", function(e){
        e.preventDefault();
        let message = document.getElementById("message").value.trim();
        if(message === "") return;

        fetch(`/chat/${roomId}/send`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value
            },
            body: JSON.stringify({message})
        }).then(() => {
            document.getElementById("message").value = "";
        });
    });
</script>
@endsection
