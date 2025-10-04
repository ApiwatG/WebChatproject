<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
       protected $fillable = ['reporter_id', 'offender_id', 'message'];

    public function participant()
    {
        return $this->belongsTo(RoomParticipant::class);
    }

    public function reporter()
    {
        return $this->belongsTo(User::class, 'reporter_id');
    }

    public function offender()
    {
        return $this->belongsTo(User::class, 'offender_id');
    }
}