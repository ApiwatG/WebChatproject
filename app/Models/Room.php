<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Room extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'max_users',
    ];

    public function users()
    {
        return $this->belongsToMany(\App\Models\User::class, 'room_participants', 'room_id', 'user_id')
                    ->withTimestamps()
                    ->withPivot('is_inroom');
    }

    public function activeUsers()
    {
        return $this->users()->wherePivot('is_inroom', true);
    }

    public function isFull()
    {
        return $this->activeUsers()->count() >= $this->max_users;
    }
}

