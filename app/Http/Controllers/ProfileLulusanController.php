<?php

namespace App\Http\Controllers;

use App\Models\ProfileLulusan;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class ProfileLulusanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $profileLulusan = ProfileLulusan::orderBy('id', 'asc')->get();
        return view('profile-lulusan', compact('profileLulusan'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:4|unique:mst_pl,id',
            'desk' => 'required|string|max:255|unique:mst_pl,desk',
            'unsur' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        ProfileLulusan::create($request->all());

        return redirect()->route('profile-lulusan')->with('success', 'Data berhasil ditambahkan!');
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
            'id' => 'required|string|max:4|unique:mst_pl,id,' . $profileLulusan->id . ',id',
            'desk' => 'required|string|max:255|unique:mst_pl,desk,'. $profileLulusan->desk,
            'unsur' => 'nullable|string|max:255',
            'keterangan' => 'nullable|string',
        ]);

        $profileLulusan->update($request->all());

        return redirect()->route('profile-lulusan')->with('success', 'Data berhasil diupdate!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(ProfileLulusan $profileLulusan)
    {
        try {
            $profileLulusan->delete();
            return redirect()->route('profile-lulusan')->with('success', 'Data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('profile-lulusan')
                    ->withErrors(['error' => 'Data tidak dapat dihapus karena masih digunakan di tabel lain (tbl_pl_cpl). Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('profile-lulusan')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple resources.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mst_pl,id',
        ]);

        try {
            $deletedCount = ProfileLulusan::whereIn('id', $request->ids)->delete();
            return redirect()->route('profile-lulusan')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('profile-lulusan')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain (tbl_pl_cpl). Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('profile-lulusan')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
