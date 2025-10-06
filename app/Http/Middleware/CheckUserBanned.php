<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckUserBanned
{
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->is_active) {
            // Store ban reason before logging out
            $banReason = Auth::user()->ban_reason ?? 'Violation of terms';
            
            // Logout the user
            Auth::guard('web')->logout();
            
            // Invalidate the session
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            
            return redirect()->route('login')
                ->with('error', 'Your account has been banned. Reason: ' . $banReason);
        }

        return $next($request);
    }
}