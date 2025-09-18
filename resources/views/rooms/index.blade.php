<h1>Rooms</h1>

@foreach($rooms as $room)
    <div>
        <strong>{{ $room->name }}</strong> ({{ $room->users_count }}/{{ $room->max_users }})
        @if($room->isFull())
            <span>Full</span>
        @else
            <form method="POST" action="{{ route('rooms.join', $room->id) }}">
                @csrf
                <button type="submit">Join</button>
            </form>
        @endif
    </div>
@endforeach

<form method="POST" action="{{ route('rooms.create') }}">
    @csrf
    <input type="text" name="name" placeholder="Room Name" required>
    <button type="submit">Create Room</button>
</form>
