<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();
            Auth::user()->update(['last_login_at' => now()]);
            return redirect()->intended('/');
        }

        return back()->withErrors([
            'email' => __('messages.invalid_credentials'),
        ])->onlyInput('email');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }

    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(Request $request)
    {
        $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'digits:10'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'password' => Hash::make($request->password),
            'role' => 'broker',
            'joined_date' => Carbon::now(),
        ]);

        Auth::login($user);

        return redirect()->route('home');
    }

    /**
     * Redirect the user to the Google authentication page.
     */
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain user info from Google and ask for more details if needed.
     */
    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->user();

            $user = User::where('google_id', $googleUser->getId())->first();

            // If the user already exists, log them in
            if ($user) {
                Auth::login($user);
                return redirect()->route('home');
            }

            // Otherwise, store Google data in session and ask for their phone number
            session([
                'google_user' => [
                    'id' => $googleUser->getId(),
                    'name' => $googleUser->getName(),
                    'email' => $googleUser->getEmail(),
                ]
            ]);

            return redirect()->route('register.phone');

        } catch (\Exception $e) {
            return redirect()->route('login')->withErrors(['email' => 'Unable to login using Google. Please try again.']);
        }
    }

    /**
     * Show the form for the user to enter their phone number.
     */
    public function showPhoneNumberForm()
    {
        if (!session()->has('google_user')) {
            return redirect()->route('register');
        }

        return view('auth.register-phone');
    }

    /**
     * Store the phone number and create the final user account.
     */
    public function storePhoneNumber(Request $request)
    {
        if (!session()->has('google_user')) {
            return redirect()->route('register');
        }

        $request->validate([
            'phone_number' => ['required', 'string', 'digits:10'],
        ]);

        $googleUser = session('google_user');

        // Prevent creating duplicate users based on email
        $existingUser = User::where('email', $googleUser['email'])->first();
        if ($existingUser) {
            return redirect()->route('login')->withErrors(['email' => 'An account with this email already exists. Please log in.']);
        }

        $user = User::create([
            'google_id' => $googleUser['id'],
            'full_name' => $googleUser['name'],
            'email' => $googleUser['email'],
            'phone_number' => $request->phone_number,
            'password' => Hash::make(uniqid()),
            'role' => 'broker',
            'joined_date' => Carbon::now(),
        ]);

        session()->forget('google_user');

        Auth::login($user);

        return redirect()->route('home');
    }
}