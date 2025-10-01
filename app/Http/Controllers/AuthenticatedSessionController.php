<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Providers\RouteServiceProvider;

class AuthenticatedSessionController extends Controller
{
    public function store(Request $request)
    {
        // ล็อกอินผู้ใช้ (Jetstream/Fortify ใช้ $request->authenticate() ด้วย)
        $credentials = $request->only('email','password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = Auth::user();

            // ถ้าเป็น admin → ไป admin.dashboard
            if ($user->is_admin) {
                return redirect()->route('admin.dashboard');
            }

            // ถ้าไม่ใช่ admin → ไป HOME ปกติ
            return redirect()->intended(RouteServiceProvider::HOME);
        }

        return back()->withErrors([
            'email' => 'อีเมลหรือรหัสผ่านไม่ถูกต้อง',
        ]);
    }
}
