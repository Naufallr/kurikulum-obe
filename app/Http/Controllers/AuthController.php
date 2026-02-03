<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\User;

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
            // Find user by name field first to check if user exists
            $user = User::where('name', $validated['username'])->first();

            if (!$user) {
                return back()->withErrors([
                    'login' => 'Username atau password salah.',
                ])->withInput($request->except('password'));
            }

            // Get raw password from database (bypass 'hashed' cast)
            // The User model has 'password' => 'hashed' cast, so we need to get the raw value
            $storedPassword = $user->getRawOriginal('password');

            // Check if password exists
            if (empty($storedPassword)) {
                Log::error('User has no password', ['user_id' => $user->id]);
                return back()->withErrors([
                    'login' => 'Akun tidak valid. Silakan hubungi administrator.',
                ])->withInput($request->except('password'));
            }

            // Check if password is already hashed (bcrypt starts with $2y$)
            $isPasswordHashed = str_starts_with($storedPassword, '$2y$') || str_starts_with($storedPassword, '$2a$') || str_starts_with($storedPassword, '$2b$');

            $passwordValid = false;

            if ($isPasswordHashed) {
                // Password is hashed, use Hash::check
                $passwordValid = Hash::check($validated['password'], $storedPassword);
            } else {
                // Password is plain text, compare directly
                $passwordValid = $validated['password'] === $storedPassword;
            }

            if ($passwordValid) {
                // If password was plain text, hash it and save to database
                if (!$isPasswordHashed) {
                    $user->password = Hash::make($validated['password']);
                    $user->save();
                }

                Auth::login($user, $request->boolean('remember'));
                $request->session()->regenerate();
                return redirect()->intended('/dashboard')->with('success', 'Login berhasil!');
            }

            return back()->withErrors([
                'login' => 'Username atau password salah.',
            ])->withInput($request->except('password'));
        } catch (\Exception $e) {
            // Log the error for debugging
            Log::error('Login error: ' . $e->getMessage(), [
                'username' => $validated['username'] ?? 'unknown',
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
