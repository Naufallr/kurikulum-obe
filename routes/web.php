<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileLulusanController;
use App\Http\Controllers\CapaianLulusanProdiController;
use App\Http\Controllers\MataKuliahController;

Route::get('/landing', function () {
    return view('landing');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/dashboard', [ProfileLulusanController::class, 'index'])->name('dashboard');
Route::post('/dashboard', [ProfileLulusanController::class, 'store'])->name('mst_pl.store');
Route::get('/dashboard/{profileLulusan}', [ProfileLulusanController::class, 'show'])->name('mst_pl.show');
Route::put('/dashboard/{profileLulusan}', [ProfileLulusanController::class, 'update'])->name('mst_pl.update');
Route::delete('/dashboard/{profileLulusan}', [ProfileLulusanController::class, 'destroy'])->name('mst_pl.destroy');
Route::post('/dashboard/delete-multiple', [ProfileLulusanController::class, 'destroyMultiple'])->name('mst_pl.destroy-multiple');

Route::get('/capaian-lulusan-prodi', [CapaianLulusanProdiController::class, 'index'])->name('capaian-lulusan-prodi.index');
Route::post('/capaian-lulusan-prodi', [CapaianLulusanProdiController::class, 'store'])->name('capaian-lulusan-prodi.store');
Route::get('/capaian-lulusan-prodi/{capaianLulusanProdi}', [CapaianLulusanProdiController::class, 'show'])->name('capaian-lulusan-prodi.show');
Route::put('/capaian-lulusan-prodi/{capaianLulusanProdi}', [CapaianLulusanProdiController::class, 'update'])->name('capaian-lulusan-prodi.update');
Route::delete('/capaian-lulusan-prodi/{capaianLulusanProdi}', [CapaianLulusanProdiController::class, 'destroy'])->name('capaian-lulusan-prodi.destroy');
Route::post('/capaian-lulusan-prodi/delete-multiple', [CapaianLulusanProdiController::class, 'destroyMultiple'])->name('capaian-lulusan-prodi.destroy-multiple');

Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah.index');
route::post('/mata-kuliah',[MataKuliahController::class,'store'])->name('mata-kuliah.store');
route::get('/mata-kuliah/{mataKuliah}',[MataKuliahController::class,'show'])->name('mata-kuliah.show');
route::put('/mata-kuliah/{mataKuliah}',[MataKuliahController::class,'update'])->name('mata-kuliah.update');
route::delete('/mata-kuliah/{mataKuliah}',[MataKuliahController::class,'destroy'])->name('mata-kuliah.destroy');
