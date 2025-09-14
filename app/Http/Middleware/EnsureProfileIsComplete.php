<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EnsureProfileIsComplete
{
    public function handle(Request $request, Closure $next)
    {
        if (Auth::check()) {
            $user = Auth::user();
            if (is_null($user->phone_number) && !$request->routeIs('profile.settings','profile.settings.update')) {
                return redirect()->route('profile.settings')->with('profile_incomplete', true);
            }
        }
        return $next($request);
    }
}