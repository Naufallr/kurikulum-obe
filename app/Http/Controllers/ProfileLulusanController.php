<?php

namespace App\Http\Controllers;

use App\Models\ProfileLulusan;
use Illuminate\Http\Request;

class ProfileLulusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profileLulusan = ProfileLulusan::orderBy('id', 'desk')->get();
        return view('dashboard', compact('profileLulusan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'kode_pl' => 'required|string|max:50|unique:profile_lulusan,kode_pl',
            'profile_lulusan' => 'required|string|max:255',
            'unsur' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        ProfileLulusan::create($request->all());

        return redirect()->route('dashboard')->with('success', 'Data berhasil ditambahkan!');
    }

    /**
     * Display the specified resource.
     */
    public function show(ProfileLulusan $profileLulusan)
    {
        return response()->json($profileLulusan);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, ProfileLulusan $profileLulusan)
    {
        $request->validate([
            'kode_pl' => 'required|string|max:50|unique:profile_lulusan,kode_pl,' . $profileLulusan->id,
            'profile_lulusan' => 'required|string|max:255',
            'unsur' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $profileLulusan->update($request->all());

        return redirect()->route('dashboard')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfileLulusan $profileLulusan)
    {
        $profileLulusan->delete();

        return redirect()->route('dashboard')->with('success', 'Data berhasil dihapus!');
    }

    /**
     * Delete multiple resources.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:profile_lulusan,id',
        ]);

        ProfileLulusan::whereIn('id', $request->ids)->delete();

        return redirect()->route('dashboard')->with('success', 'Data berhasil dihapus!');
    }
}
