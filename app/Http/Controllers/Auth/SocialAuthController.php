<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Str;

class SocialAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    public function callback()
    {
        $googleUser = Socialite::driver('google')->stateless()->user();

        $user = User::firstOrCreate(
            ['email' => $googleUser->getEmail()],
            [
                'full_name' => $googleUser->getName() ?: $googleUser->getNickname() ?: 'New User',
                'password'  => bcrypt(Str::random(32)),
                // phone_number left null; user will fill it
            ]
        );

        $isNew = $user->wasRecentlyCreated;

        Auth::login($user, true);

        // If you want to always send new OR existing to profile when phone missing:
        if ($isNew || empty($user->phone_number)) {
            return redirect()
                ->route('profile.settings')
                ->with('onboarding', true);
        }

        // Otherwise normal intended redirect
        return redirect()->intended(route('profile.settings'));
    }
}