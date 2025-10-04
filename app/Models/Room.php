<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Room extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'max_users'];

    

    public function users()
    {
        return $this->belongsToMany(User::class, 'room_participants')
                ->withPivot('joined_at', 'left_at')
                ->withTimestamps();
    }

    public function isFull(): bool
    {
        return $this->users()->count() >= $this->max_users;
    }
}

