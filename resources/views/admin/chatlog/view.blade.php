@extends('layouts.admin')

@section('title', 'Chat Room Detail')

@push('styles')
<link rel="stylesheet" href="{{ asset('/css/chatlogview.css') }}">
@endpush

@section('content')
<a href="{{ route('admin.chatlog.index') }}" class="back-btn">
    Back to Chat Log
</a>

<div class="room-detail-card">
    <div class="room-header">
        <h1 class="room-title">{{ $room->room_name }}</h1>
        <span class="room-code-badge">{{ $room->room_code }}</span>
    </div>
    
    <div class="detail-grid">
        <div class="detail-box">
            <div class="detail-label">Host</div>
            <div class="detail-value">{{ $room->host->name }}</div>
        </div>
        
        <div class="detail-box">
            <div class="detail-label">Member Count</div>
            <div class="detail-value">{{ $room->member_count }} Players</div>
        </div>

        <div class="detail-box">
            <div class="detail-label">Created At</div>
            <div class="detail-value">{{ $room->created_at->format('M d, Y h:i A') }}</div>
        </div>
        
        <div class="detail-box">
            <div class="detail-label">Last Activity</div>
            <div class="detail-value">{{ $room->last_activity->diffForHumans() }}</div>
        </div>
    </div>
</div>

<div class="members-card">
    <h2 class="section-title">Room Members</h2>
    @if($room->members->count() > 0)
        <div class="member-grid">
            @foreach($room->members as $member)
            <div class="member-item">
                <div class="member-avatar">
                    {{ substr($member->name, 0, 1) }}
                </div>
                <div class="member-details">
                    <div class="member-name">{{ $member->name }}</div>
                    <div class="member-email">{{ $member->email }}</div>
                </div>
            </div>
            @endforeach
        </div>
    @else
        <p style="color: #999;">No members in this room yet.</p>
    @endif
</div>
@endsection
