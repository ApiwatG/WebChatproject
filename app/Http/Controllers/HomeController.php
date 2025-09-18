<?php
// app/Http/Controllers/HomeController.php
namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('home');
    }

    public function join(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:50'
        ]);

        session()->flash('success', 'Welcome home, ' . $request->name . '! 🏠');
        
        return redirect()->back();
    }
}