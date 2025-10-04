<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class user_cosmetic extends Model
{
    use HasFactory;

     public function cosmetic()
    {
        return $this->belongsTo(cosmetic::class);
    }

    public function User()
    {
        return $this->belongsTo(User::class);
    }
}

