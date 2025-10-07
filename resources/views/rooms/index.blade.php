<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rooms - Game Lobby</title>
    <link rel="stylesheet" href="{{ asset('css/room.css') }}">
</head>
<body>
    <x-toast />
    <div class="container">
        <a href="{{ route('dashboard')}}" class="btn-back">← Back</a>
        
        <h1>Game Rooms</h1>

        <div class="quick-join-section">
            <div class="feature-card blue">
                <h3>🎲 Quick Join</h3>
                <p>Join a random available room instantly</p>
                <form method="POST" action="{{ route('rooms.quickJoin') }}">
                    @csrf
                    <button type="submit" class="btn-blue btn-full">Quick Join</button>
                </form>
            </div>

      
            <div class="feature-card purple">
                <h3>🔑 Join by Room ID</h3>
                <p>Enter a room ID or code to join</p>
                <form method="POST" action="{{ route('rooms.joinByCode') }}">
                    @csrf
                    <div class="input-group">
                        <input type="text" name="room_code" placeholder="Enter Room ID" required>
                        <button type="submit" class="btn-purple">Join</button>
                    </div>
                </form>
            </div>
        </div>

 
        <h2 class="section-title">Available Rooms</h2>
        <div class="rooms-list">
            @forelse($rooms as $room)
                <div class="room-card">
                    <div class="room-info">
                        <strong>{{ $room->name }}</strong>
                        <span class="room-count">
                            ({{ $room->active_users_count ?? 0 }}/{{ $room->max_users }})
                        </span>
                        <span class="room-id">ID: {{ $room->id }}</span>
                        @if($room->isFull())
                            <span class="status-badge status-full">🔒 Full</span>
                        @else
                            <span class="status-badge status-available">✓ Available</span>
                        @endif
                    </div>
                    @if(!$room->isFull())
                        <form method="POST" action="{{ route('rooms.join', $room->id) }}">
                            @csrf
                            <button type="submit" class="btn-blue">Join</button>
                        </form>
                    @endif
                </div>
            @empty
                <div class="empty-state">No rooms available. Create one below!</div>
            @endforelse
        </div>


        <div class="feature-card green">
            <h3>➕ Create a New Room</h3>
            <form method="POST" action="{{ route('rooms.store') }}">
                @csrf
                <div class="input-group">
                    <input 
                        type="text" 
                        name="name" 
                        placeholder="Room Name" 
                        required 
                        maxlength="50">
                    <button type="submit" class="btn-green">Create Room</button>
                </div>
            </form>
        </div>
    </div>

    
</body>
</html>