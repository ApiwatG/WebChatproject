<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'coins',
        'is_active',
        'banned_at',
        'ban_reason',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'banned_at' => 'datetime',
        'coins' => 'integer',
        'is_active' => 'boolean',
    ];

    protected $appends = [
        'profile_photo_url',
    ];

   
    public function isBanned()
    {
        return !$this->is_active;
    }

    
    public function ban($reason = null)
    {
        $this->update([
            'is_active' => false,
            'banned_at' => now(),
            'ban_reason' => $reason,
        ]);
    }

   
    public function unban()
    {
        $this->update([
            'is_active' => true,
            'banned_at' => null,
            'ban_reason' => null,
        ]);
    }

    public function rooms()
    {
        return $this->belongsToMany(Room::class, 'room_participants')
            ->withPivot('joined_at', 'left_at')
            ->withTimestamps();
    }

    public function cosmetics()
    {
        return $this->belongsToMany(Cosmetic::class, 'user_cosmetics', 'user_id', 'cosmetic_id')
            ->withPivot('is_equipped', 'acquired_at')
            ->withTimestamps();
    }

    public function equippedCosmetics()
    {
        return $this->belongsToMany(Cosmetic::class, 'user_cosmetics', 'user_id', 'cosmetic_id')
            ->wherePivot('is_equipped', true)
            ->withPivot('is_equipped', 'acquired_at')
            ->withTimestamps();
    }

  
    public function getEquippedCosmeticByType($typeId)
    {
        return $this->equippedCosmetics()
            ->where('cosmetic_type_id', $typeId)
            ->first();
    }

    public function ownsCosmetic($cosmeticId)
    {
        return $this->cosmetics()->where('cosmetic_id', $cosmeticId)->exists();
    }

    public function equipCosmetic($cosmeticId)
    {
        $cosmetic = Cosmetic::findOrFail($cosmeticId);
        
        if (!$this->ownsCosmetic($cosmeticId)) {
            return false;
        }

        $this->cosmetics()
            ->where('cosmetic_type_id', $cosmetic->cosmetic_type_id)
            ->update(['user_cosmetics.is_equipped' => false]);

        $this->cosmetics()->updateExistingPivot($cosmeticId, [
            'is_equipped' => true
        ]);

        return true;
    }

    public function unequipCosmetic($cosmeticId)
    {
        $this->cosmetics()->updateExistingPivot($cosmeticId, [
            'is_equipped' => false
        ]);
    }

    public function purchaseCosmetic($cosmeticId)
    {
        $cosmetic = Cosmetic::findOrFail($cosmeticId);
        
        if ($this->ownsCosmetic($cosmeticId)) {
            return ['success' => false, 'message' => 'You already own this cosmetic'];
        }

        if ($this->coins < $cosmetic->price) {
            return ['success' => false, 'message' => 'Not enough coins'];
        }

        $this->decrement('coins', $cosmetic->price);

        $this->cosmetics()->attach($cosmeticId, [
            'is_equipped' => false,
            'acquired_at' => now()
        ]);

        return ['success' => true, 'message' => 'Cosmetic purchased successfully'];



    }

    public function game()
    {
        return $this->belongsToMany(Game::class);
    }
}