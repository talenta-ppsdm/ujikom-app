<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\BankSoalController;
use App\Http\Controllers\DashboardPesertaController;
use App\Http\Controllers\JadwalUjianController;
use App\Http\Controllers\PengujiController;
use App\Http\Controllers\PengumumanController;
use App\Http\Controllers\PesertaController;
use App\Http\Controllers\RiwayatPengumumanController;
use App\Http\Controllers\UjianController;
use App\Http\Controllers\RiwayatUjianController;
use App\Models\Pengumuman;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/login', function () {
    return view('login');
})->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    Route::get('/peserta', [PesertaController::class, 'index'])->name('peserta.index');
    Route::post('/peserta', [PesertaController::class, 'store'])->name('peserta.store');
    Route::put('/peserta/{id}', [PesertaController::class, 'update'])->name('peserta.update');
    Route::delete('/peserta/{id}', [PesertaController::class, 'destroy'])->name('peserta.destroy');

    Route::get('/penguji', [PengujiController::class, 'index'])->name('penguji.index');
    Route::post('/penguji', [PengujiController::class, 'store'])->name('penguji.store');
    Route::put('/penguji/{id}', [PengujiController::class, 'update'])->name('penguji.update');
    Route::delete('/penguji/{id}', [PengujiController::class, 'destroy'])->name('penguji.destroy');
    
    Route::get('/bank-soal', [BankSoalController::class, 'index'])->name('banksoal.index');
    Route::post('/bank-soal', [BankSoalController::class, 'store'])->name('banksoal.store');
    Route::get('/bank-soal/template', [BankSoalController::class, 'downloadTemplate'])->name('banksoal.template');
    Route::post('/bank-soal/import', [BankSoalController::class, 'import'])->name('banksoal.import');
    Route::put('/bank-soal/{id}', [BankSoalController::class, 'update'])->name('banksoal.update');
    Route::delete('/bank-soal/{id}', [BankSoalController::class, 'destroy'])->name('banksoal.destroy');
    
    Route::get('/jadwal-ujian', [JadwalUjianController::class, 'index'])->name('jadwal-ujian.index');
    Route::post('/jadwal-ujian', [JadwalUjianController::class, 'store'])->name('jadwal-ujian.store');
    Route::put('/jadwal-ujian/{id}', [JadwalUjianController::class, 'update'])->name('jadwal-ujian.update');
    Route::delete('/jadwal-ujian/{id}', [JadwalUjianController::class, 'destroy'])->name('jadwal-ujian.destroy');
    
    Route::get('/beranda', [DashboardPesertaController::class, 'index'])->name('dashboard-peserta.index');
    
    Route::get('/ujian/{id}/mulai', [UjianController::class, 'start'])->name('ujian.start');
    Route::post('/ujian/simpan', [UjianController::class, 'saveAnswer'])->name('ujian.saveAnswer');
    Route::post('/ujian/selesai', [UjianController::class, 'finish'])->name('ujian.finish');
    Route::get('/ujian/{id}/hasil', [UjianController::class, 'result'])->name('ujian.result');
    
    Route::get('/pengumuman', [PengumumanController::class, 'index'])->name('pengumuman.index');
    Route::post('/pengumuman', [PengumumanController::class, 'store'])->name('pengumuman.store');
    Route::put('/pengumuman/{id}', [PengumumanController::class, 'update'])->name('pengumuman.update');
    Route::delete('/pengumuman/{id}', [PengumumanController::class, 'destroy'])->name('pengumuman.destroy');
    
    Route::get('/riwayat-ujian', [RiwayatUjianController::class, 'index'])->name('riwayat-ujian.index');
    Route::get('riwayat-pengumuman', [RiwayatPengumumanController::class, 'index'])->name('riwayat-pengumuman.index');
    Route::post('/riwayat-pengumuman/{id}/read', [RiwayatPengumumanController::class, 'markAsRead'])->name('pengumuman.markAsRead');
});



