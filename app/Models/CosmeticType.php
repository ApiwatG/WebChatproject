<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CosmeticType extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cosmetic_type';

    protected $fillable = ['cosmetictype_name'];

    public function cosmetics()
    {
        return $this->hasMany(Cosmetic::class);
    }
}