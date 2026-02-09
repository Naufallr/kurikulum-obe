<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Show the login form.
     */
    public function login(Request $request)
    {
        // Redirect if already authenticated
        if (Auth::check()) {
            return redirect()->intended('/dashboard');
        }

        // Only show the login view on GET
        if ($request->isMethod('get')) {
            return view('login');
        }

        // Handle login form POST
        $validated = $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        try {
            $credentials = [
                'name' => $validated['username'],
                'password' => $validated['password'],
            ];

            if (Auth::attempt($credentials, $request->boolean('remember'))) {
                $request->session()->regenerate();
                return redirect()->intended('/dashboard')->with('success', 'Login berhasil!');
            }

            return back()->withErrors([
                'login' => 'Username atau password salah.',
            ])->withInput($request->except('password'));
        } catch (\Exception $e) {
            Log::error('Login error: ' . $e->getMessage(), [
                'login' => $validated['username'] ?? 'unknown',
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ]);

            return back()->withErrors([
                'login' => 'Terjadi kesalahan saat login. Silakan coba lagi atau hubungi administrator.',
            ])->withInput($request->except('password'));
        }
    }

    /**
     * Log the user out and redirect to login page.
     */
    public function logout()
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('login');
    }
}
