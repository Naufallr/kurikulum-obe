<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MataKuliahController;
use App\Http\Controllers\BahanKajianController;
use App\Http\Controllers\ProfileLulusanController;
use App\Http\Controllers\CapaianLulusanProdiController;
use App\Http\Controllers\PemetaanCplPlController;
use App\Http\Controllers\PemetaanCplBkController;
use App\Http\Controllers\PemetaanBkMkController;
use App\Http\Controllers\PemetaanCplMkController;
use App\Http\Controllers\PemetaanCplBkMkController;
use App\Http\Controllers\OrganisasiMataKuliahController;
use App\Http\Controllers\PetaPemenuhanCplController;
use App\Http\Controllers\PemetaanCplCpmkMkController;
use App\Http\Controllers\PemetaanSubcpmkController;
use App\Http\Controllers\TahapMekanismeController;
use App\Http\Controllers\BobotPenilaianController;

Route::get('/landing', function () {
    return view('landing');
});

Route::match(['get', 'post'], '/login', [AuthController::class, 'login'])->name('login');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

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

Route::get('/pemetaan-cpl-pl', [PemetaanCplPlController::class, 'index'])->name('pemetaan-cpl-pl.index');
Route::post('/pemetaan-cpl-pl', [PemetaanCplPlController::class, 'store'])->name('pemetaan-cpl-pl.store');
Route::get('/pemetaan-cpl-bk', [PemetaanCplBkController::class, 'index'])->name('pemetaan-cpl-bk.index');
Route::post('/pemetaan-cpl-bk', [PemetaanCplBkController::class, 'store'])->name('pemetaan-cpl-bk.store');
Route::get('/pemetaan-bk-mk', [PemetaanBkMkController::class, 'index'])->name('pemetaan-bk-mk.index');
Route::post('/pemetaan-bk-mk', [PemetaanBkMkController::class, 'store'])->name('pemetaan-bk-mk.store');
Route::get('/pemetaan-cpl-mk', [PemetaanCplMkController::class, 'index'])->name('pemetaan-cpl-mk.index');
Route::post('/pemetaan-cpl-mk', [PemetaanCplMkController::class, 'store'])->name('pemetaan-cpl-mk.store');
Route::get('/pemetaan-cpl-bk-mk', [PemetaanCplBkMkController::class, 'index'])->name('pemetaan-cpl-bk-mk.index');
Route::post('/pemetaan-cpl-bk-mk', [PemetaanCplBkMkController::class, 'store'])->name('pemetaan-cpl-bk-mk.store');
Route::get('/organisasi-mata-kuliah', [OrganisasiMataKuliahController::class, 'index'])->name('organisasi-mata-kuliah.index');
Route::get('/peta-pemenuhan-cpl', [PetaPemenuhanCplController::class, 'index'])->name('peta-pemenuhan-cpl.index');

Route::get('/bahan-kajian', [BahanKajianController::class, 'index'])->name('bahan-kajian.index');
Route::post('/bahan-kajian', [BahanKajianController::class, 'store'])->name('bahan-kajian.store');
Route::get('/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'show'])->name('bahan-kajian.show');
Route::put('/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'update'])->name('bahan-kajian.update');
Route::delete('/bahan-kajian/{bahanKajian}', [BahanKajianController::class, 'destroy'])->name('bahan-kajian.destroy');
Route::post('/bahan-kajian/delete-multiple', [BahanKajianController::class, 'destroyMultiple'])->name('bahan-kajian.destroy-multiple');

Route::get('/mata-kuliah', [MataKuliahController::class, 'index'])->name('mata-kuliah.index');
route::post('/mata-kuliah',[MataKuliahController::class,'store'])->name('mata-kuliah.store');
route::get('/mata-kuliah/{mataKuliah}',[MataKuliahController::class,'show'])->name('mata-kuliah.show');
route::put('/mata-kuliah/{mataKuliah}',[MataKuliahController::class,'update'])->name('mata-kuliah.update');
route::delete('/mata-kuliah/{mataKuliah}',[MataKuliahController::class,'destroy'])->name('mata-kuliah.destroy');
route::post('/mata-kuliah/delete-multiple', [MataKuliahController::class, 'destroyMultiple'])->name('mata-kuliah.destroy-multiple');

Route::get('/pemetaan-cpl-cpmk-mk', [PemetaanCplCpmkMkController::class, 'index'])->name('pemetaan-cpl-cpmk-mk.index');
Route::post('/pemetaan-cpl-cpmk-mk', [PemetaanCplCpmkMkController::class, 'store'])->name('pemetaan-cpl-cpmk-mk.store');
Route::get('/pemetaan-cpl-cpmk-mk/{pemetaanCplCpmkMk}', [PemetaanCplCpmkMkController::class, 'show'])->name('pemetaan-cpl-cpmk-mk.show');
Route::put('/pemetaan-cpl-cpmk-mk/{pemetaanCplCpmkMk}', [PemetaanCplCpmkMkController::class, 'update'])->name('pemetaan-cpl-cpmk-mk.update');
Route::delete('/pemetaan-cpl-cpmk-mk/{pemetaanCplCpmkMk}', [PemetaanCplCpmkMkController::class, 'destroy'])->name('pemetaan-cpl-cpmk-mk.destroy');
Route::post('/pemetaan-cpl-cpmk-mk/delete-multiple', [PemetaanCplCpmkMkController::class, 'destroyMultiple'])->name('pemetaan-cpl-cpmk-mk.destroy-multiple');

Route::get('/pemetaan-subcpmk', [PemetaanSubcpmkController::class, 'index'])->name('pemetaan-subcpmk.index');
Route::post('/pemetaan-subcpmk', [PemetaanSubcpmkController::class, 'store'])->name('pemetaan-subcpmk.store');
Route::get('/pemetaan-subcpmk/{pemetaanSubcpmk}', [PemetaanSubcpmkController::class, 'show'])->name('pemetaan-subcpmk.show');
Route::put('/pemetaan-subcpmk/{pemetaanSubcpmk}', [PemetaanSubcpmkController::class, 'update'])->name('pemetaan-subcpmk.update');
Route::delete('/pemetaan-subcpmk/{pemetaanSubcpmk}', [PemetaanSubcpmkController::class, 'destroy'])->name('pemetaan-subcpmk.destroy');
Route::post('/pemetaan-subcpmk/delete-multiple', [PemetaanSubcpmkController::class, 'destroyMultiple'])->name('pemetaan-subcpmk.destroy-multiple');

Route::get('/tahap-mekanisme-penilaian', [TahapMekanismeController::class, 'index'])->name('tahap-mekanisme-penilaian.index');
Route::post('/tahap-mekanisme-penilaian', [TahapMekanismeController::class, 'store'])->name('tahap-mekanisme-penilaian.store');
Route::get('/tahap-mekanisme-penilaian/{tahapMekanisme}', [TahapMekanismeController::class, 'show'])->name('tahap-mekanisme-penilaian.show');
Route::put('/tahap-mekanisme-penilaian/{tahapMekanisme}', [TahapMekanismeController::class, 'update'])->name('tahap-mekanisme-penilaian.update');
Route::delete('/tahap-mekanisme-penilaian/{tahapMekanisme}', [TahapMekanismeController::class, 'destroy'])->name('tahap-mekanisme-penilaian.destroy');
Route::post('/tahap-mekanisme-penilaian/delete-multiple', [TahapMekanismeController::class, 'destroyMultiple'])->name('tahap-mekanisme-penilaian.destroy-multiple');

Route::get('/bobot-penilaian', [BobotPenilaianController::class, 'index'])->name('bobot-penilaian.index');
Route::post('/bobot-penilaian', [BobotPenilaianController::class, 'store'])->name('bobot-penilaian.store');
Route::get('/bobot-penilaian/get-data/{bobotPenilaian}', [BobotPenilaianController::class, 'show'])->name('bobot-penilaian.show');
Route::put('/bobot-penilaian/{bobotPenilaian}', [BobotPenilaianController::class, 'update'])->name('bobot-penilaian.update');
Route::delete('/bobot-penilaian/{bobotPenilaian}', [BobotPenilaianController::class, 'destroy'])->name('bobot-penilaian.destroy');
Route::post('/bobot-penilaian/delete-multiple', [BobotPenilaianController::class, 'destroyMultiple'])->name('bobot-penilaian.destroy-multiple');
