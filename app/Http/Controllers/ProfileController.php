<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }

    public function update(Request $request)
    {
        if (!Auth::check()) {
            abort(401);
        }

        $validated = $request->validate([
            'fakultas' => 'nullable|string|max:255',
            'program_studi' => 'nullable|string|max:255',
            'akreditasi' => 'nullable|string|max:255',
            'jenjang_pendidikan' => 'nullable|string|max:255',
            'gelar_lulusan' => 'nullable|string|max:255',
            'visi_keilmuan_program_studi' => 'nullable|string',
            'misi_program_studi' => 'nullable|string',
        ]);

        Auth::user()->update($validated);

        return redirect()
            ->route('dashboard')
            ->with('success', 'Profil program studi berhasil diperbarui.');
    }
}
