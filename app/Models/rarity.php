<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class rarity extends Model
{
    use HasFactory;
    
    public function cosmetics()
{
    return $this->belongsToMany(cosmetics::class);
}

}
