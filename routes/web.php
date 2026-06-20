<?php
// routes/web.php

use App\Http\Controllers\AlternatifController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\KriteriaController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PenilaianController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RangkingController;
use App\Http\Controllers\AbsensiController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register']);
    Route::get('/forgot-password', [AuthController::class, 'showForgotForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [AuthController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    
    // Profile - semua user bisa akses
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    
    // Data Siswa - semua user bisa akses
    Route::resource('alternatif', AlternatifController::class);
    
    // Penilaian - semua user bisa akses
Route::prefix('penilaian')->name('penilaian.')->group(function () {
    Route::get('/', [PenilaianController::class, 'index'])->name('index');
    Route::get('/create', [PenilaianController::class, 'create'])->name('create');
    Route::post('/', [PenilaianController::class, 'store'])->name('store');
    Route::get('/{alternatif}/edit', [PenilaianController::class, 'edit'])->name('edit');
    Route::put('/{alternatif}', [PenilaianController::class, 'update'])->name('update');
    Route::delete('/{alternatif}/{kriteria}', [PenilaianController::class, 'destroy'])->name('destroy');
    
    // ROUTE EXPORT PDF DAN CSV
    Route::get('/export-pdf', [PenilaianController::class, 'exportPDF'])->name('export-pdf');
    Route::get('/export-csv', [PenilaianController::class, 'exportCSV'])->name('export-csv');
});
    
    // Rangking - semua user bisa akses
    Route::get('/rangking', [RangkingController::class, 'index'])->name('rangking.index');
    
    // Laporan - semua user bisa akses
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/', [LaporanController::class, 'index'])->name('index');
        Route::get('/cetak-pdf', [LaporanController::class, 'cetakPDF'])->name('cetak-pdf');
    });
    
    // ============================================================
    // ABSENSI ROUTES - SEMUA USER BISA AKSES (termasuk user/guru)
    // ============================================================
    Route::prefix('absensi')->name('absensi.')->group(function () {
        // Semua user bisa melihat
        Route::get('/', [AbsensiController::class, 'index'])->name('index');
        Route::get('/rekap/{siswaId}', [AbsensiController::class, 'rekap'])->name('rekap');
        Route::get('/rekap-bulanan', [AbsensiController::class, 'rekapBulanan'])->name('rekap-bulanan');
        
        // Semua user juga bisa create, edit, delete (user/guru bisa input absensi)
        Route::get('/create/{siswaId?}', [AbsensiController::class, 'create'])->name('create');
        Route::post('/', [AbsensiController::class, 'store'])->name('store');
        Route::get('/mass-create', [AbsensiController::class, 'massCreate'])->name('mass-create');
        Route::post('/mass-store', [AbsensiController::class, 'massStore'])->name('mass-store');
        Route::get('/{id}/edit', [AbsensiController::class, 'edit'])->name('edit');
        Route::put('/{id}', [AbsensiController::class, 'update'])->name('update');
        Route::delete('/{id}', [AbsensiController::class, 'destroy'])->name('destroy');
        Route::delete('/siswa/{siswaId}/destroy-all', [AbsensiController::class, 'destroyAll'])->name('destroy-all');
        Route::post('/sync', [AbsensiController::class, 'syncAll'])->name('sync');
        
        // ROUTE BARU: Hapus semua data absensi
        Route::delete('/destroy-all-absensi', [AbsensiController::class, 'destroyAllAbsensi'])->name('destroy-all-absensi');
    });
    
    // ============================================================
    // KRITERIA ROUTES - User hanya bisa melihat, Admin bisa edit
    // ============================================================
    // Semua user bisa melihat kriteria
    Route::prefix('kriteria')->name('kriteria.')->group(function () {
        Route::get('/', [KriteriaController::class, 'index'])->name('index');
        Route::get('/{kriteria}/sub', [KriteriaController::class, 'subKriteria'])->name('sub');
    });
    
    // Hanya admin yang bisa edit kriteria
    Route::middleware(['admin'])->prefix('kriteria')->name('kriteria.')->group(function () {
        Route::get('/create', [KriteriaController::class, 'create'])->name('create');
        Route::post('/', [KriteriaController::class, 'store'])->name('store');
        Route::get('/{kriteria}/edit', [KriteriaController::class, 'edit'])->name('edit');
        Route::put('/{kriteria}', [KriteriaController::class, 'update'])->name('update');
        Route::delete('/{kriteria}', [KriteriaController::class, 'destroy'])->name('destroy');
        Route::post('/{kriteria}/sub', [KriteriaController::class, 'storeSubKriteria'])->name('sub.store');
        Route::delete('/sub-kriteria/{id}', [KriteriaController::class, 'destroySubKriteria'])->name('sub.destroy');
    });
    
    // Sync kehadiran - semua user bisa akses
    Route::post('/penilaian/sync-kehadiran', [PenilaianController::class, 'syncKehadiran'])->name('penilaian.sync-kehadiran');
});

Route::get('/', function () {
    return redirect()->route('login');
});