
<link rel="stylesheet" href="{{ asset('css/inroom.css') }}">
<div class="wrap">
    {{-- Left Scene Area --}}
    <div class="scene">
        {{-- Your game/content area here --}}
        <div class="minilogo">
            <img src="{{ asset('img/logo.png') }}" alt="Game Logo" onerror="this.style.display='none'">
        </div>
    </div>

    {{-- Right Chat Panel --}}
    <div class="chatPanel">
        <div class="chatHeader">
            <h2>{{ $room->name }}</h2>
            <div class="uid">Room #{{ $room->id }}</div>
        </div>

        <div class="userList">
            <strong>Users ({{ $room->users()->count() }}/{{ $room->max_users }}):</strong>
            @foreach($room->users as $user)
                {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
            @endforeach
        </div>

        <div id="messages" class="messages">
            {{-- Messages will load here --}}
        </div>

        <form id="chat-form" class="inputRow">
            @csrf
            <input 
                type="text" 
                id="message" 
                placeholder="Type your message..." 
                autocomplete="off"
            >
            <button type="submit">Send</button>
        </form>
    </div>
</div>

{{-- Sound Toggle Button --}}
<div class="sound" onclick="toggleSound()">
    🔊
</div>

<script>
    const roomId = "{{ $room->id }}";
    const currentUser = "{{ auth()->user()->name }}";
    let soundEnabled = true;

    function toggleSound() {
        soundEnabled = !soundEnabled;
        document.querySelector('.sound').textContent = soundEnabled ? '🔊' : '🔇';
    }

    function addMessage(user, message, isSent = false) {
        const messagesDiv = document.getElementById("messages");
        const msgDiv = document.createElement('div');
        msgDiv.className = isSent ? 'msg sent' : 'msg recv';
        
        if (!isSent) {
            msgDiv.innerHTML = `<strong>${user}:</strong> ${message}`;
        } else {
            msgDiv.innerHTML = `<strong>You:</strong> ${message}`;
        }
        
        messagesDiv.appendChild(msgDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    // Load cached messages
    fetch(`/chat/${roomId}/messages`)
        .then(res => res.json())
        .then(data => {
            data.forEach(msg => {
                const isSent = msg.user === currentUser;
                addMessage(msg.user, msg.message, isSent);
            });
        })
        .catch(err => console.error('Failed to load messages:', err));

    // Listen for new messages
    if (window.Echo) {
        window.Echo.join(`chat.${roomId}`)
            .here((users) => {
                console.log('Users currently in room:', users);
            })
            .joining((user) => {
                console.log(user.name + ' joined the room');
            })
            .leaving((user) => {
                console.log(user.name + ' left the room');
            })
            .listen('MessageSent', (e) => {
                addMessage(e.user, e.message, false);
                
                // Optional: Play sound notification
                if (soundEnabled && e.user !== currentUser) {
                    // You can add a notification sound here
                    console.log('New message received!');
                }
            });
    } else {
        console.error("Echo not initialized. Check bootstrap.js and run 'npm run dev'");
    }

    // Send message
    document.getElementById("chat-form").addEventListener("submit", function(e) {
        e.preventDefault();
        
        const messageInput = document.getElementById("message");
        const message = messageInput.value.trim();
        
        if (message === "") return;

        // Add message immediately to UI
        addMessage(currentUser, message, true);

        fetch(`/chat/${roomId}/send`, {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": document.querySelector("input[name='_token']").value
            },
            body: JSON.stringify({ message })
        })
        .then(response => response.json())
        .then(data => {
            console.log('Message sent successfully');
        })
        .catch(err => {
            console.error('Failed to send message:', err);
        });

        messageInput.value = "";
    });

    // Allow Enter to send message
    document.getElementById("message").addEventListener("keypress", function(e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            document.getElementById("chat-form").dispatchEvent(new Event('submit'));
        }
    });
</script>