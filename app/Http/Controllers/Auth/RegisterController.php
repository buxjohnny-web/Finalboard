<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\Request;

class RegisterController extends Controller
{
    protected function redirectTo()
    {
        // override default redirect path
        return route('profile.settings');
    }

    public function store(Request $request)
    {
        $request->validate([
            'full_name' => ['required','string','max:255'],
            'email'     => ['required','email','max:255','unique:users,email'],
            'password'  => ['required','confirmed','min:8'],
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email'     => $request->email,
            'password'  => Hash::make($request->password),
        ]);

        event(new Registered($user));
        Auth::login($user);

        // explicit redirect (ensures even if trait fallback changes)
        return redirect()->route('profile.settings')->with('onboarding', true);
    }
}