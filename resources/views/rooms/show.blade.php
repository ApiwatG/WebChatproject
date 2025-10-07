<<<<<<< Updated upstream
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $room->name }} - Chat Room</title>
    <link rel="stylesheet" href="{{ asset('css/inroom.css') }}">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/axios/1.6.4/axios.min.js"></script>
    @vite(['resources/js/app.js'])
</head>

<body>

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

            <div class="userList" id="userList">
                <strong>Users ({{ $room->users()->count() }}/{{ $room->max_users }}):</strong>
                @foreach($room->users as $user)
                    {{ $user->name }}{{ !$loop->last ? ', ' : '' }}
                @endforeach
            </div>

            <div id="messages" class="messages">
                {{-- Messages will load here --}}
            </div>

            <form id="chat-form" class="inputRow">
                <input type="text" id="message" placeholder="Type your message..." autocomplete="off" maxlength="500"
                    required>
                <button type="submit">Send</button>
            </form>
=======
<head>@vite(['resources/js/app.js'])</head>
<link rel="stylesheet" href="{{ asset('css/inroom.css') }}">
<div class="wrap">
    {{-- Left Scene Area --}}
    <div class="scene">
        {{-- Your game/content area here --}}
        <div class="minilogo">
            <img src="{{ asset('img/logo.png') }}" alt="Game Logo" onerror="this.style.display='none'">
>>>>>>> Stashed changes
        </div>
    </div>

    {{-- Sound Toggle Button --}}
    <div class="sound" onclick="toggleSound()">
        🔊
    </div>

    <script>
        const roomId = "{{ $room->id }}";
        const currentUser = "{{ auth()->user()->name }}";
        const currentUserId = {{ auth()->id() }};
        let soundEnabled = true;

<<<<<<< Updated upstream
        function toggleSound() {
            soundEnabled = !soundEnabled;
            document.querySelector('.sound').textContent = soundEnabled ? '🔊' : '🔇';
        }
    </script>
=======
<script>
    const roomId = "{{ $room->id }}";
    const currentUser = "{{ auth()->user()->name }}";
    let soundEnabled = true;

    function toggleSound() {
        soundEnabled = !soundEnabled;
        document.querySelector('.sound').textContent = soundEnabled ? '🔊' : '🔇';
    }

    function addMessage(user, message, isSent = false, time = null) {
        const messagesDiv = document.getElementById("messages");
        const msgDiv = document.createElement('div');
        msgDiv.className = isSent ? 'msg sent' : 'msg recv';
        
        if (!isSent) {
            msgDiv.innerHTML = `<strong>${user}:</strong> ${message}`;
        } else {
            msgDiv.innerHTML = `<strong>You:</strong> ${message}`;
        }
            if (time) {
            msgDiv.innerHTML += `<small>${time}</small>`;
        }
        
        messagesDiv.appendChild(msgDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }
>>>>>>> Stashed changes

<<<<<<< Updated upstream
    <!-- Removed Pusher JS and chat.js, use only Echo for real-time chat -->
    <script>
        const roomId = "{{ $room->id }}";
        const currentUser = "{{ auth()->user()->name }}";
        const currentUserId = {{ auth()->id() }};
        let soundEnabled = true;

        // Debug Echo connection
        if (window.Echo) {
            console.log('Echo loaded:', window.Echo);
            window.Echo.connector.pusher.connection.bind('connected', function () {
                console.log('Pusher connected!');
=======
    function addSystemMessage(message) {
        const messagesDiv = document.getElementById("messages");
        const msgDiv = document.createElement('div');
        msgDiv.className = 'msg system';
        msgDiv.innerHTML = message;
        messagesDiv.appendChild(msgDiv);
        messagesDiv.scrollTop = messagesDiv.scrollHeight;
    }

    function updateUserList(users) {
        const userListDiv = document.querySelector('.userList');
        const userCount = users.length;
        const maxUsers = {{ $room->max_users }};
        userListDiv.innerHTML = `<strong>Users (${userCount}/${maxUsers}):</strong> `;
        userListDiv.innerHTML += users.map(user => user.name).join(', ');
    }

    // Load cached messages
    fetch(`/chat/${roomId}/messages`)
        .then(res => res.json())
        .then(data => {
            data.forEach(msg => {
                const isSent = msg.user === currentUser;
                addMessage(msg.user, msg.message, isSent);
>>>>>>> Stashed changes
            });
            window.Echo.join(`chat.${roomId}`)
                .here((users) => {
                    console.log('Users in room:', users);
                })
                .joining((user) => {
                    console.log('User joined:', user);
                })
                .leaving((user) => {
                    console.log('User left:', user);
                })
                .listen('MessageSent', (e) => {
                    console.log('MessageSent event received:', e);
                    if (e.user === currentUser) {
                        addMessage(e.user, e.message, true, e.time, 'received');
                    } else {
                        addMessage(e.user, e.message, false, e.time, 'received');
                        if (soundEnabled) {
                            const audio = new Audio('/sounds/notification.mp3');
                            audio.play();
                        }
                    }
                });
        } else {
            console.error('Echo not loaded!');
        }

<<<<<<< Updated upstream
        // Send Message Function
        async function sendMessage(e) {
            e.preventDefault();
            const messageInput = document.getElementById('message');
            const message = messageInput.value.trim();

            if (!message) return;

            try {
                const response = await fetch(`/chat/${roomId}/send`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({ message })
                });

                if (response.ok) {
                    messageInput.value = '';
                } else {
                    throw new Error('Failed to send message');
                }
            } catch (error) {
                console.error('Failed to send message:', error);
                alert('Failed to send message. Please try again.');
            }        // Toggle Sound
            function toggleSound() {
                soundEnabled = !soundEnabled;
                document.querySelector('.sound').textContent = soundEnabled ? '🔊' : '🔇';
            }

            // Escape HTML to prevent XSS
            function escapeHtml(text) {
                const map = {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#039;'
                };
                return String(text).replace(/[&<>"']/g, m => map[m]);
            }

            // Add Message to UI
            function addMessage(user, message, isSent = false, time = null, status = 'received') {
                const messagesDiv = document.getElementById("messages");
                const msgDiv = document.createElement('div');
                msgDiv.className = isSent ? 'msg sent' : 'msg recv';

                const timeStr = time ? time : '';
                let statusHtml = '';
                if (isSent) {
                    if (status === 'sending') {
                        statusHtml = `<span class="sending-status">⏳ กำลังส่ง...</span>`;
                    } else if (status === 'received') {
                        statusHtml = `<span class="sending-status">✅ ส่งแล้ว</span>`;
                    }
                }

                if (!isSent) {
                    msgDiv.innerHTML = `
                        <strong>${escapeHtml(user)}:</strong> 
                        <span>${escapeHtml(message)}</span>
                        ${timeStr ? `<small class="time">${timeStr}</small>` : ''}
                    `;
                } else {
                    msgDiv.innerHTML = `
                        <strong>You:</strong> 
                        <span>${escapeHtml(message)}</span>
                        ${timeStr ? `<small class="time">${timeStr}</small>` : ''}
                        ${statusHtml}
                    `;
                }

                messagesDiv.appendChild(msgDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }

            // Add System Message
            function addSystemMessage(message) {
                const messagesDiv = document.getElementById("messages");
                const msgDiv = document.createElement('div');
                msgDiv.className = 'msg system';
                msgDiv.innerHTML = `<em>${escapeHtml(message)}</em>`;
                messagesDiv.appendChild(msgDiv);
                messagesDiv.scrollTop = messagesDiv.scrollHeight;
            }

            // Update User List
            function updateUserList(users) {
                const userListEl = document.getElementById('userList');
                if (userListEl && users) {
                    const userNames = users.map(u => escapeHtml(u.name)).join(', ');
                    userListEl.innerHTML = `
                <strong>Users (${users.length}/{{ $room->max_users }}):</strong> 
                ${userNames}
            `;
                }
            }

            // Play Notification Sound
            function playNotificationSound() {
                try {
                    const audioContext = new (window.AudioContext || window.webkitAudioContext)();
                    const oscillator = audioContext.createOscillator();
                    const gainNode = audioContext.createGain();

                    oscillator.connect(gainNode);
                    gainNode.connect(audioContext.destination);

                    oscillator.frequency.value = 800;
                    oscillator.type = 'sine';

                    gainNode.gain.setValueAtTime(0.3, audioContext.currentTime);
                    gainNode.gain.exponentialRampToValueAtTime(0.01, audioContext.currentTime + 0.1);

                    oscillator.start(audioContext.currentTime);
                    oscillator.stop(audioContext.currentTime + 0.1);
                } catch (e) {
                    console.error('Cannot play sound:', e);
                }
            }

            // Load cached messages
            fetch(`/chat/${roomId}/messages`)
                .then(res => res.json())
                .then(data => {
                    if (Array.isArray(data)) {
                        data.forEach(msg => {
                            const isSent = msg.user === currentUser;
                            addMessage(msg.user, msg.message, isSent, msg.time);
                        });
                    }
                })
                .catch(err => console.error('Failed to load messages:', err));

            // Listen for new messages via Laravel Echo
            if (typeof window.Echo !== 'undefined') {
                console.log('✅ Echo loaded, joining channel...');

                window.Echo.join(`chat.${roomId}`)
                    .here((users) => {
                        console.log('👥 Users currently in room:', users);
                        updateUserList(users);
                    })
                    .joining((user) => {
                        console.log('➕', user.name, 'joined');
                        addSystemMessage(`${user.name} joined the room`);
                        // Refresh user list
                        fetch(`/chat/${roomId}/messages`).catch(() => { });
                    })
                    .leaving((user) => {
                        console.log('➖', user.name, 'left');
                        addSystemMessage(`${user.name} left the room`);
                    })
                    .listen('MessageSent', (e) => {
                        console.log('MessageSent event received:', e);
                        if (e.user === currentUser) {
                            addMessage(e.user, e.message, true, e.time, 'received');
                        } else {
                            addMessage(e.user, e.message, false, e.time, 'received');
                            if (soundEnabled) {
                                const audio = new Audio('/sounds/notification.mp3');
                                audio.play();
                            }
                        }
                    })
                    .error((error) => {
                        console.error('❌ Channel error:', error);
                    });

            } else {
                console.error("❌ Echo not initialized!");
                console.error("Make sure to:");
                console.error("1. Run: npm install");
                console.error("2. Run: npm run dev");
                console.error("3. Configure .env properly");
                console.error("4. Start WebSocket server");

                // Show warning to user
                addSystemMessage("⚠️ Real-time chat is not available. Messages will not update automatically.");
            }

            // Send message
            document.getElementById("chat-form").addEventListener("submit", function (e) {
                e.preventDefault();

                const messageInput = document.getElementById("message");
                const message = messageInput.value.trim();

                if (message === "") return;

                const sendBtn = this.querySelector('button[type="submit"]');
                const originalText = sendBtn.textContent;
                sendBtn.disabled = true;
                sendBtn.textContent = 'Sending...';

                // Add message with 'sending' status
                addMessage(currentUser, message, true, new Date(), 'sending');

                fetch(`/chat/${roomId}/send`, {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                        "Accept": "application/json"
                    },
                    body: JSON.stringify({ message })
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Failed to send message');
                        }
                        return response.json();
                    })
                    .then(data => {
                        console.log('✅ Message sent:', data);
                    })
                    .catch(err => {
                        console.error('❌ Failed to send message:', err);
                        alert('Failed to send message. Please try again.');
                    })
                    .finally(() => {
                        sendBtn.disabled = false;
                        sendBtn.textContent = originalText;
                    });

                messageInput.value = "";
            });

            // Allow Enter to send message
            document.getElementById("message").addEventListener("keypress", function (e) {
                if (e.key === "Enter" && !e.shiftKey) {
                    e.preventDefault();
                    document.getElementById("chat-form").dispatchEvent(new Event('submit'));
                }
=======
    // Listen for new messages
    let channel;
    if (window.Echo) {
<<<<<<< Updated upstream
        console.log('Echo initialized, joining channel:', `chat.${roomId}`);
        window.Echo.join(`chat.${roomId}`)
=======
        channel = window.Echo.join(`chat.${roomId}`)
>>>>>>> Stashed changes
            .here((users) => {
                console.log('Users currently in room:', users);
                // อัพเดทรายชื่อผู้ใช้ที่อยู่ในห้อง
                updateUserList(users);
            })
            .joining((user) => {
                console.log(user.name + ' joined the room');
                // แสดงข้อความแจ้งเตือนเมื่อมีผู้ใช้เข้าร่วม
                addSystemMessage(`${user.name} joined the room`);
            })
            .leaving((user) => {
                console.log(user.name + ' left the room');
                // แสดงข้อความแจ้งเตือนเมื่อมีผู้ใช้ออก
                addSystemMessage(`${user.name} left the room`);
            })
<<<<<<< Updated upstream
<<<<<<< Updated upstream
            .listen('MessageSent', (e) => {
                addMessage(e.user, e.message, e.user === currentUser, e.time);
>>>>>>> Stashed changes
            });

            // Cleanup when leaving page
            window.addEventListener('beforeunload', () => {
                if (typeof window.Echo !== 'undefined') {
                    Echo.leave(`chat.${roomId}`);
=======
=======
>>>>>>> Stashed changes
            .listen('.MessageSent', (e) => {
                console.log('Message received:', e);
                addMessage(e.user, e.message, e.user === currentUser);
                
                // Optional: Play sound notification
                if (soundEnabled && e.user !== currentUser) {
                    // You can add a notification sound here
                    console.log('New message received!');
>>>>>>> Stashed changes
                }
            });

            // Auto-focus message input
            document.getElementById("message").focus();
    </script>

<<<<<<< Updated upstream
</body>

</html>
=======
        const formData = new FormData();
        formData.append('message', message);
        formData.append('_token', document.querySelector("input[name='_token']").value);

        fetch(`/chat/${roomId}/send`, {
            method: "POST",
            headers: {
                "X-Requested-With": "XMLHttpRequest",
                "Accept": "application/json"
            },
            body: formData
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
>>>>>>> Stashed changes
