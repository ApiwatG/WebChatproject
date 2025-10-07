@extends('layouts.admin')

@section('content')
<h1 class="page-title">Chat Log</h1>

<div class="table-wrapper">
    @if($rooms->count() > 0)
        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Code</th>
                    <th>Host</th>
                    <th>Members</th>
                    <th>Last Activity</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($rooms as $room)
                <tr>
                    <td><code>{{ $room->room_code }}</code></td>
                    <td>{{ $room->host->name }}</td>
                    <td>{{ $room->member_count }}</td>
                    <td>{{ $room->last_activity->diffForHumans() }}</td>
                    <td>
                        <a href="{{ route('admin.chatlog.show', $room->id) }}" class="view-btn">
                            View
                        </a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="empty-message">No rooms found</div>
    @endif
</div>
@endsection
