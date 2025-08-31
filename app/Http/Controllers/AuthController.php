<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            // optional: update last login
            Auth::user()->update(['last_login_at' => now()]);

            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ]);
    }

    public function logout()
    {
        Auth::logout();
        return redirect('/login');
    }
}