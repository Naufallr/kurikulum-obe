<?php

namespace App\Http\Controllers;

use App\Models\MataKuliah;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;


class MataKuliahController extends Controller
{
    /**
     * Display a listing of the Mata Kuliah.
     */
    public function index()
    {
        $mataKuliah = MataKuliah::orderBy('id', 'asc')->get();
        return view('mata-kuliah', compact('mataKuliah'));
    }

    /**
     * Store a newly created Mata Kuliah in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:20,kode',
            'nama_mk' => 'required|string|max:255|unique:mst_mk,nama_mk',
            'sks' => 'required|integer|min:1',
        ]);

        MataKuliah::create($request->all());

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata Kuliah berhasil ditambahkan!');
    }

    /**
     * Display the specified Mata Kuliah.
     */
    public function show(MataKuliah $mataKuliah)
    {
        return response()->json($mataKuliah);
    }

    /**
     * Update the specified Mata Kuliah in storage.
     */
    public function update(Request $request, MataKuliah $mataKuliah)
    {
        $request->validate([
            'id' => 'required|string|max:20',
            'nama_mk' => 'required|string|max:255|unique:mst_mk,nama_mk,' . $mataKuliah->nama_mk,
            'jenis_mk' => 'required|string|max:255',
            'status' => 'required|string|max:30',
        ]);

        $mataKuliah->update($request->all());

        return redirect()->route('mata-kuliah.index')->with('success', 'Mata Kuliah berhasil diupdate!');
    }

    /**
     * Remove the specified Mata Kuliah from storage.
     */
    public function destroy(MataKuliah $mataKuliah)
    {
        try {
            $mataKuliah->delete();
            return redirect()->route('mata-kuliah.index')->with('success', 'Mata Kuliah berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('mata-kuliah.index')
                    ->withErrors(['error' => 'Mata Kuliah tidak dapat dihapus karena masih digunakan di tabel lain. Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('mata-kuliah.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple Mata Kuliah.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:mata_kuliah,id',
        ]);

        try {
            $deletedCount = MataKuliah::whereIn('id', $request->ids)->delete();
            return redirect()->route('mata-kuliah.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('mata-kuliah.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain. Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('mata-kuliah.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

}
