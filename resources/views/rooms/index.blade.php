@extends('layouts.app')

@section('content')
<button class="btn-join"><a href="{{route('dashboard')}}"><-</a></button>
<h1 class="text-2xl font-bold mb-4">Rooms</h1>

@foreach($rooms as $room)
<div class="border p-3 mb-3 rounded">
    <strong>{{ $room->name }}</strong> ({{ $room->users_count }}/{{ $room->max_users }})
    @if($room->isFull())
        <span class="text-red-500 ml-2">Full</span>
    @else
        <form method="POST" action="{{ route('rooms.join', $room->id) }}" class="inline">
            @csrf
            <button type="submit" class="bg-blue-500 text-white px-3 py-1 rounded">Join</button>
        </form>
    @endif
</div>
@endforeach

<h2 class="text-xl font-semibold mt-6 mb-2">Create a New Room</h2>
<form method="POST" action="{{ route('rooms.store') }}">
    @csrf
    <input type="text" name="name" placeholder="Room Name" required class="border px-2 py-1 rounded w-64">
    <button type="submit" class="bg-green-500 text-white px-4 py-1 rounded ml-2">Create Room</button>
</form>
@endsection
