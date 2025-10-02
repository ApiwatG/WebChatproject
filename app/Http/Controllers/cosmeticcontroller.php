<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class cosmeticcontroller extends Controller
{
    public function index()
    {
        return view('cosmetic');
    }
}
