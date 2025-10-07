<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class RoomParticipant extends Model
{
    use HasFactory,SoftDeletes;

     protected $fillable = ['room_id', 'user_id', 'is_inroom','offender_id','reporter_id'];
    public function room()
    {
        return $this->belongsTo(Room::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function reportsAsMaker()
    {
        return $this->hasMany(Report::class, 'reporter_id');
    }
    
    public function reportsAgainst()
    {
        return $this->hasMany(Report::class, 'offender_id');
    
}
}