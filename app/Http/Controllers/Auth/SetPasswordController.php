<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules\Password;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class SetPasswordController extends Controller
{
    public function showSetPasswordForm(Request $request)
    {
        // If user is logged in, use their account
        if (Auth::check()) {
            return view('auth.set-password');
        }

        // If not logged in, show a form that asks for email
        return view('auth.set-password-guest');
    }

    public function setPassword(Request $request)
    {
        $request->validate([
            'email' => ['required', 'email', 'exists:users,email'],
            'password' => ['required', 'confirmed', Password::defaults()],
        ]);

        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors(['email' => 'Email not found.']);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        // If user was not logged in, log them in with new password
        if (!Auth::check()) {
            Auth::login($user);
        }

        return redirect()->route('dashboard')
            ->with('status', 'Password has been updated successfully.');
    }
} 