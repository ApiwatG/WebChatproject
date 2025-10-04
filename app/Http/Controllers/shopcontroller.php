<?php

namespace App\Http\Controllers;

use App\Models\Cosmetic;
use App\Models\Rarity;
use App\Models\CosmeticType;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get filter parameters
        $rarityFilter = $request->get('rarity');
        $typeFilter = $request->get('type');
        $search = $request->get('search');
        
        // Build query
        $query = Cosmetic::with(['rarity', 'cosmeticType']);
        
        if ($rarityFilter) {
            $query->where('rarity_id', $rarityFilter);
        }
        
        if ($typeFilter) {
            $query->where('cosmetic_type_id', $typeFilter);
        }
        
        if ($search) {
            $query->where('cosmetic_name', 'like', '%' . $search . '%');
        }
        
        $cosmetics = $query->get();
        
        // Get user's owned cosmetic IDs
        $ownedCosmeticIds = $user->cosmetics()->pluck('cosmetic_id')->toArray();
        
        // Get filter options
        $rarities = Rarity::all();
        $types = CosmeticType::all();
        
        return view('shop', compact('cosmetics', 'ownedCosmeticIds', 'rarities', 'types', 'user'));
    }
    
    public function purchase($cosmeticId)
    {
        $user = auth()->user();
        $result = $user->purchaseCosmetic($cosmeticId);
        
        if ($result['success']) {
            return response()->json($result);
        }
        
        return response()->json($result, 400);
    }
}
