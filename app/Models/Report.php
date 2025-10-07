<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Report extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'reporter_id',
        'offender_id',
        'message',
        'Report_message',  
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function reporterParticipant()
    {
        return $this->belongsTo(RoomParticipant::class, 'reporter_id');
    }
    
    public function offenderParticipant()
    {
        return $this->belongsTo(RoomParticipant::class, 'offender_id');
    }

    public function getReporterAttribute()
    {
        return $this->reporterParticipant->user ?? null;
    }

    public function getOffenderAttribute()
    {
        return $this->offenderParticipant->user ?? null;
    }
}