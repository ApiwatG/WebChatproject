<?php

namespace App\Http\Controllers;

use App\Models\Cosmetic;
use App\Models\CosmeticType;
use Illuminate\Http\Request;

class CosmeticController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Get search and filter parameters
        $search = $request->get('search');
        $typeFilter = $request->get('type');
        
        // Get user's owned cosmetics
        $query = $user->cosmetics()->with(['cosmeticType', 'rarity']);
        
        if ($search) {
            $query->where('cosmetic_name', 'like', '%' . $search . '%');
        }
        
        if ($typeFilter) {
            $query->where('cosmetic_type_id', $typeFilter);
        }
        
        $cosmetics = $query->get();
        $cosmeticTypes = CosmeticType::all();
        
        return view('cosmetic', compact('cosmetics', 'cosmeticTypes', 'user'));
    }
    
    public function equip($cosmeticId)
    {
        $user = auth()->user();
        
        if ($user->equipCosmetic($cosmeticId)) {
            return response()->json([
                'success' => true,
                'message' => 'Cosmetic equipped successfully'
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'You do not own this cosmetic'
        ], 403);
    }
    
    public function unequip($cosmeticId)
    {
        $user = auth()->user();
        $user->unequipCosmetic($cosmeticId);
        
        return response()->json([
            'success' => true,
            'message' => 'Cosmetic unequipped'
        ]);
    }
}