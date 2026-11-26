<?php

use App\Http\Controllers\Api\BahanKajianApiController;
use App\Http\Controllers\Api\BobotPenilaianApiController;
use App\Http\Controllers\Api\CapaianLulusanProdiApiController;
use App\Http\Controllers\Api\CapaianPembelajaranMataKuliahApiController;
use App\Http\Controllers\Api\MappingMatrixApiController;
use App\Http\Controllers\Api\MataKuliahApiController;
use App\Http\Controllers\Api\PemetaanCplCpmkMkApiController;
use App\Http\Controllers\Api\PemetaanSubcpmkApiController;
use App\Http\Controllers\Api\ProfileLulusanApiController;
use App\Http\Controllers\Api\SubCpmkApiController;
use App\Http\Controllers\Api\TahapMekanismeApiController;
use Illuminate\Support\Facades\Route;

Route::name('api.')->group(function () {
    Route::get('/', function () {
        return response()->json([
            'success' => true,
            'message' => 'REST API Kurikulum OBE tersedia.',
            'data' => [
                'resources' => [
                    'profile-lulusan',
                    'capaian-lulusan-prodi',
                    'bahan-kajian',
                    'mata-kuliah',
                    'capaian-pembelajaran-mata-kuliah',
                    'sub-cpmk',
                    'tahap-mekanisme-penilaian',
                    'bobot-penilaian',
                    'pemetaan-cpl-cpmk-mk',
                    'pemetaan-subcpmk',
                ],
                'mappings' => [
                    'mappings/cpl-pl',
                    'mappings/cpl-bk',
                    'mappings/bk-mk',
                    'mappings/cpl-mk',
                    'mappings/cpl-bk-mk',
                ],
            ],
        ]);
    })->name('index');

    Route::apiResource('profile-lulusan', ProfileLulusanApiController::class)->parameter('profile-lulusan', 'record');
    Route::apiResource('capaian-lulusan-prodi', CapaianLulusanProdiApiController::class)->parameter('capaian-lulusan-prodi', 'record');
    Route::apiResource('bahan-kajian', BahanKajianApiController::class)->parameter('bahan-kajian', 'record');
    Route::apiResource('mata-kuliah', MataKuliahApiController::class)->parameter('mata-kuliah', 'record');
    Route::apiResource('capaian-pembelajaran-mata-kuliah', CapaianPembelajaranMataKuliahApiController::class)->parameter('capaian-pembelajaran-mata-kuliah', 'record');
    Route::apiResource('sub-cpmk', SubCpmkApiController::class)->parameter('sub-cpmk', 'record');
    Route::apiResource('tahap-mekanisme-penilaian', TahapMekanismeApiController::class)->parameter('tahap-mekanisme-penilaian', 'record');
    Route::apiResource('bobot-penilaian', BobotPenilaianApiController::class)->parameter('bobot-penilaian', 'record');
    Route::apiResource('pemetaan-cpl-cpmk-mk', PemetaanCplCpmkMkApiController::class)->parameter('pemetaan-cpl-cpmk-mk', 'record');
    Route::apiResource('pemetaan-subcpmk', PemetaanSubcpmkApiController::class)->parameter('pemetaan-subcpmk', 'record');

    Route::get('mappings/cpl-pl', [MappingMatrixApiController::class, 'cplPlIndex'])->name('mappings.cpl-pl.index');
    Route::put('mappings/cpl-pl', [MappingMatrixApiController::class, 'cplPlReplace'])->name('mappings.cpl-pl.replace');
    Route::get('mappings/cpl-bk', [MappingMatrixApiController::class, 'cplBkIndex'])->name('mappings.cpl-bk.index');
    Route::put('mappings/cpl-bk', [MappingMatrixApiController::class, 'cplBkReplace'])->name('mappings.cpl-bk.replace');
    Route::get('mappings/bk-mk', [MappingMatrixApiController::class, 'bkMkIndex'])->name('mappings.bk-mk.index');
    Route::put('mappings/bk-mk', [MappingMatrixApiController::class, 'bkMkReplace'])->name('mappings.bk-mk.replace');
    Route::get('mappings/cpl-mk', [MappingMatrixApiController::class, 'cplMkIndex'])->name('mappings.cpl-mk.index');
    Route::put('mappings/cpl-mk', [MappingMatrixApiController::class, 'cplMkReplace'])->name('mappings.cpl-mk.replace');
    Route::get('mappings/cpl-bk-mk', [MappingMatrixApiController::class, 'cplBkMkIndex'])->name('mappings.cpl-bk-mk.index');
    Route::put('mappings/cpl-bk-mk', [MappingMatrixApiController::class, 'cplBkMkReplace'])->name('mappings.cpl-bk-mk.replace');
});
