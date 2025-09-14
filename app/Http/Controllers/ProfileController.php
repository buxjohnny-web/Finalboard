<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function edit()
    {
        $user = Auth::user();
        return view('profile.edit', compact('user'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'full_name'   => ['required','string','max:255'],
            'phone_number'=> ['nullable','string','max:25'],
            // add any other required fields
        ]);

        $user->update($request->only(['full_name','phone_number']));

        return redirect()
            ->route('profile.settings')
            ->with('success', __('Profile updated successfully.'));
    }
}