<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class cosmetic extends Model
{
    use HasFactory;

      
    public function rooms()
{
    return $this->belongsToMany(Room::class);
}
   public function rarity()
    {
        return $this->belongsTo(rarity::class);
    }
}
