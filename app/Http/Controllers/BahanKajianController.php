<?php

namespace App\Http\Controllers;

use App\Models\BahanKajian;
use Illuminate\Http\Request;
use Illuminate\Database\QueryException;

class BahanKajianController extends Controller
{
    /**
     * Display a listing of the Bahan Kajian.
     */
    public function index()

    {
        $bahanKajian = BahanKajian::orderBy('id', 'asc')->get();
        return view('bahan-kajian', compact('bahanKajian'));
    }

    /**
     * Store a newly created Bahan Kajian in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'id' => 'required|string|max:10',
            'bahan_kajian' => 'required|string|max:255|unique:table_bk,bahan_kajian',
            'kategori' => 'required|string|max:255',
            'referensi' => 'required|string|max:255',
        ]);

        BahanKajian::create($request->all());

        return redirect()->route('bahan-kajian.index')->with('success', 'Bahan Kajian berhasil ditambahkan!');
    }

    /**
     * Display the specified Bahan Kajian.
     */
    public function show(BahanKajian $bahanKajian)
    {
        return response()->json($bahanKajian);
    }

    /**
     * Update the specified Bahan Kajian in storage.
     */
    public function update(Request $request, BahanKajian $bahanKajian)
    {
        $request->validate([
            'id' => 'required|string|max:10',
            'bahan_kajian' => 'required|string|max:255|unique:table_bk,bahan_kajian,' . $bahanKajian->bahan_kajian . ',bahan_kajian',
            'kategori' => 'required|string|max:255',
            'referensi' => 'required|string|max:255',
        ]);

        $bahanKajian->update($request->all());

        return redirect()->route('bahan-kajian.index')->with('success', 'Bahan Kajian berhasil diupdate!');
    }

    /**
     * Remove the specified Bahan Kajian from storage.
     */
    public function destroy(BahanKajian $bahanKajian)
    {
        try {
            $bahanKajian->delete();
            return redirect()->route('bahan-kajian.index')->with('success', 'Bahan Kajian berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('bahan-kajian.index')
                    ->withErrors(['error' => 'Mata Kuliah tidak dapat dihapus karena masih digunakan di tabel lain. Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('bahan-kajian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

    /**
     * Delete multiple Bahan Kajian.
     */
    public function destroyMultiple(Request $request)
    {
        $request->validate([
            'ids' => 'required|array',
            // PK mst_mk adalah nama_mk
            'ids.*' => 'exists:table_bk,id',
        ]);

        try {
            $deletedCount = BahanKajian::whereIn('id', $request->ids)->delete();
            return redirect()->route('bahan-kajian.index')->with('success', $deletedCount . ' data berhasil dihapus!');
        } catch (QueryException $e) {
            if (str_contains($e->getMessage(), '23503') || str_contains($e->getMessage(), 'foreign key constraint')) {
                return redirect()->route('bahan-kajian.index')
                    ->withErrors(['error' => 'Beberapa data tidak dapat dihapus karena masih digunakan di tabel lain. Silakan hapus data yang terkait terlebih dahulu.']);
            }
            return redirect()->route('bahan-kajian.index')
                ->withErrors(['error' => 'Gagal menghapus data: ' . $e->getMessage()]);
        }
    }

}

