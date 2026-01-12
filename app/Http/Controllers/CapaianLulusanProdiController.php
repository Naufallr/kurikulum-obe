<?php

namespace App\Http\Controllers;

use App\Models\CapaianLulusanProdi;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class CapaianLulusanProdiController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $capaianLulusanProdi = CapaianLulusanProdi::orderBy('id', 'asc')->get();
        return view('capaian-lulusan-prodi', compact('capaianLulusanProdi'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:5|unique:mst_cplp,id',
            'desk' => 'required|string|unique:mst_cplp,desk',
            'aspek' => 'nullable|string|max:255',
        ]);

        try {
            CapaianLulusanProdi::create($request->all());
            return redirect()->route('capaian-lulusan-prodi.index')->with('success', 'Data berhasil ditambahkan!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23505') || str_contains($e->getMessage(), 'unique constraint')) {
                return redirect()->route('capaian-lulusan-prodi.index')
                    ->withErrors(['error' => 'Data dengan deskripsi tersebut sudah ada. Deskripsi CPL harus unik.'])
                    ->withInput();
            }
            return redirect()->route('capaian-lulusan-prodi.index')
                ->withErrors(['error' => 'Gagal menambahkan data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(CapaianLulusanProdi $capaianLulusanProdi)
    {
        return response()->json($capaianLulusanProdi);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, CapaianLulusanProdi $capaianLulusanProdi)
    {
        $request->validate([
            'id' => 'required|string|max:5|unique:mst_cplp,id,' . $capaianLulusanProdi->id,
            'desk' => 'required|string|unique:mst_cplp,desk,' . $capaianLulusanProdi->id . ',id',
            'aspek' => 'nullable|string|max:255',
        ]);

        try {
            $capaianLulusanProdi->update($request->all());
            return redirect()->route('capaian-lulusan-prodi.index')->with('success', 'Data berhasil diupdate!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23505') || str_contains($e->getMessage(), 'unique constraint')) {
                return redirect()->route('capaian-lulusan-prodi.index')
                    ->withErrors(['error' => 'Data dengan deskripsi tersebut sudah ada. Deskripsi CPL harus unik.'])
                    ->withInput();
            }
            return redirect()->route('capaian-lulusan-prodi.index')
                ->withErrors(['error' => 'Gagal mengupdate data: ' . $e->getMessage()])
                ->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(CapaianLulusanProdi $capaianLulusanProdi)
    {
        try {
            $capaianLulusanProdi->delete();
            return redirect()->route('capaian-lulusan-prodi.index')->with('success', 'Data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('capaian-lulusan-prodi.index')
                    ->withErrors(['error' => 'Data tidak dapat dihapus karena masih digunakan di tabel lain. Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('capaian-lulusan-prodi.index')
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
            'ids.*' => 'exists:mst_cplp,id',
        ]);

        try {
            $deletedCount = CapaianLulusanProdi::whereIn('id', $request->ids)->delete();
            return redirect()->route('capaian-lulusan-prodi.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('capaian-lulusan-prodi.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain. Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('capaian-lulusan-prodi.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }
}
