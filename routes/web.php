<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileLulusanController;

Route::get('/landing', function () {
    return view('landing');
});

Route::get('/login', function () {
    return view('login');
});

Route::get('/dashboard', [ProfileLulusanController::class, 'index'])->name('dashboard');
Route::post('/dashboard', [ProfileLulusanController::class, 'store'])->name('profile-lulusan.store');
Route::get('/dashboard/{profileLulusan}', [ProfileLulusanController::class, 'show'])->name('profile-lulusan.show');
Route::put('/dashboard/{profileLulusan}', [ProfileLulusanController::class, 'update'])->name('profile-lulusan.update');
Route::delete('/dashboard/{profileLulusan}', [ProfileLulusanController::class, 'destroy'])->name('profile-lulusan.destroy');
Route::post('/dashboard/delete-multiple', [ProfileLulusanController::class, 'destroyMultiple'])->name('profile-lulusan.destroy-multiple');
