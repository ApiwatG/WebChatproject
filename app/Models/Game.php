<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    use HasFactory;


    protected $table = '_game';
    protected $fillable = [
        'user_id',
        'Score',
        'Win',
        'lose',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
