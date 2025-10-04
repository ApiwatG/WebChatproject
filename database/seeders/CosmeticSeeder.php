<?php
// database/seeders/CosmeticSeeder.php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Rarity;
use App\Models\CosmeticType;
use App\Models\Cosmetic;

class CosmeticSeeder extends Seeder
{
    public function run()
    {
        // Create rarities
        $common = Rarity::create(['rarity_name' => 'Common']);
        $uncommon = Rarity::create(['rarity_name' => 'Uncommon']);
        $rare = Rarity::create(['rarity_name' => 'Rare']);
        $epic = Rarity::create(['rarity_name' => 'Epic']);
        $legendary = Rarity::create(['rarity_name' => 'Legendary']);
        
        // Create cosmetic types
        $hat = CosmeticType::create(['cosmetictype_name' => 'Hat']);
        $outfit = CosmeticType::create(['cosmetictype_name' => 'Outfit']);
        $accessory = CosmeticType::create(['cosmetictype_name' => 'Accessory']);
        
        // Create sample cosmetics
        Cosmetic::create([
            'cosmetic_name' => 'Basic Cap',
            'price' => 100,
            'rarity_id' => $common->id,
            'cosmetic_type_id' => $hat->id,
        ]);
        
        Cosmetic::create([
            'cosmetic_name' => 'Cool Sunglasses',
            'price' => 250,
            'rarity_id' => $uncommon->id,
            'cosmetic_type_id' => $accessory->id,
        ]);
        
        Cosmetic::create([
            'cosmetic_name' => 'Stylish Suit',
            'price' => 500,
            'rarity_id' => $rare->id,
            'cosmetic_type_id' => $outfit->id,
        ]);
        
        Cosmetic::create([
            'cosmetic_name' => 'Crown',
            'price' => 1000,
            'rarity_id' => $epic->id,
            'cosmetic_type_id' => $hat->id,
        ]);
        
        Cosmetic::create([
            'cosmetic_name' => 'Dragon Wings',
            'price' => 2500,
            'rarity_id' => $legendary->id,
            'cosmetic_type_id' => $accessory->id,
        ]);
    }
}