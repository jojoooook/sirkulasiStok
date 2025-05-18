<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class LoginController extends Controller
{
    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('pages.auth.login');
    }

    /**
     * Get the login username to be used by the controller.
     */
    public function username()
    {
        return 'username';
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'username' => ['required', 'string'],
            'password' => ['required', 'string'],
        ]);

        // Check if user exists and is active
        $user = User::where('username', $credentials['username'])->first();

        if (!$user) {
            return back()->withErrors([
                'username' => 'The provided credentials do not match our records.',
            ])->onlyInput('username');
        }

        if (!$user->active) {
            return back()->withErrors([
                'username' => 'Your account is inactive. Please contact administrator.',
            ])->onlyInput('username');
        }

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            // Setelah login berhasil, arahkan ke dashboard
            return redirect()->intended('/');  // Mengarah ke dashboard
        }

        return back()->withErrors([
            'username' => 'The provided credentials do not match our records.',
        ])->onlyInput('username');
    }

    /**
     * Log the user out.
     */
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
