<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cosmetic extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'cosmetics';

    protected $fillable = [
        'cosmetic_name',
        'price',
        'cosmetic_img',
        'rarity_id',
        'cosmetic_type_id'
    ];

    protected $casts = [
        'price' => 'integer',
    ];

    public function rarity()
    {
        return $this->belongsTo(Rarity::class, 'rarity_id');
    }

    public function cosmeticType()
    {
        return $this->belongsTo(CosmeticType::class, 'cosmetic_type_id');
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'user_cosmetics', 'cosmetic_id', 'user_id')
            ->withPivot('is_equipped', 'acquired_at')
            ->withTimestamps();
    }

    public function getImageUrlAttribute()
    {
        return $this->cosmetic_img 
            ? asset('storage/' . $this->cosmetic_img) 
            : asset('img/default-cosmetic.png');
    }

    public function scopeOfType($query, $typeId)
    {
        return $query->where('cosmetic_type_id', $typeId);
    }

    public function scopeOfRarity($query, $rarityId)
    {
        return $query->where('rarity_id', $rarityId);
    }
}