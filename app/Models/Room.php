<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'max_users',
    ];

    // map to users via your actual pivot table name
    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'room_participants', 'room_id', 'user_id')
                    ->withTimestamps()
                    ->withPivot('is_inroom');
    }

    // helper relation for currently-in-room users
    public function activeUsers()
    {
        return $this->users()->wherePivot('is_inroom', true);
    }

    public function isFull()
    {
        return $this->activeUsers()->count() >= $this->max_users;
    }
}

