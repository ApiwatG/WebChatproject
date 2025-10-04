<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class cosmeticcontroller extends Controller
{
    public function index()
    {
        return view('cosmetic');
    }

    public function store(Request $request)
{
    $request->validate([
        'cosmetic_name' => 'required',
        'price' => 'required|integer',
        'cosmetic_img' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
    ]);

    $imagePath = $request->file('cosmetic_img')->store('cosmetics', 'public');

    Cosmetic::create([
        'cosmetic_name' => $request->cosmetic_name,
        'price' => $request->price,
        'cosmetic_img' => $imagePath, 
    ]);
}
}
