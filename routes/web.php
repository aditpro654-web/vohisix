<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AdminDashboardController;
use App\Http\Controllers\AdminSiswaController;
use App\Http\Controllers\AdminDudiController;
use App\Http\Controllers\AdminBookingController;
use App\Http\Controllers\AdminLoginController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\SiswaPklController;
use App\Http\Controllers\SiswaDudiController;
use App\Http\Controllers\SiswaBookingController;
use App\Http\Controllers\WaliKelasDashboardController;
use App\Http\Controllers\KakonslDashboardController;

// Login Routes
Route::middleware('web')->group(function () {
    // allow both root and /login to display the form (makes it easier to hit via 127.0.0.1:8000/login)
    Route::get('/', [AuthController::class, 'showLogin'])->name('auth.show-login');
    Route::get('/login', [AuthController::class, 'showLogin']);
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

// Admin Routes
Route::middleware(['auth', 'is_admin'])->group(function () {
    Route::get('/admin/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
    
    Route::get('/admin/siswa/export', [AdminSiswaController::class, 'export'])->name('admin.siswa.export');
    Route::get('/admin/siswa/export/pdf', [App\Http\Controllers\PdfExportController::class, 'siswa'])->name('admin.siswa.export.pdf');
    Route::get('/admin/siswa/import/template', [AdminSiswaController::class, 'downloadImportTemplate'])->name('admin.siswa.import.template');
    Route::post('/admin/siswa/import/preview', [AdminSiswaController::class, 'importPreview'])->name('admin.siswa.import.preview');
    Route::post('/admin/siswa/import', [AdminSiswaController::class, 'import'])->name('admin.siswa.import');
    Route::resource('/admin/siswa', AdminSiswaController::class, ['names' => 'admin.siswa']);

    Route::get('/admin/dudi/export', [AdminDudiController::class, 'export'])->name('admin.dudi.export');
    Route::get('/admin/dudi/import/template', [AdminDudiController::class, 'downloadImportTemplate'])->name('admin.dudi.import.template');
    Route::post('/admin/dudi/import', [AdminDudiController::class, 'import'])->name('admin.dudi.import');
    Route::resource('/admin/dudi', AdminDudiController::class, ['names' => 'admin.dudi']);

    Route::get('/admin/booking/export', [AdminBookingController::class, 'export'])->name('admin.booking.export');
    Route::resource('/admin/booking', AdminBookingController::class, ['names' => 'admin.booking']);

    Route::get('/admin/login/export', [AdminLoginController::class, 'export'])->name('admin.login.export');
    Route::get('/admin/login/import/template', [AdminLoginController::class, 'downloadImportTemplate'])->name('admin.login.import.template');
    Route::post('/admin/login/import/preview', [AdminLoginController::class, 'previewImport'])->name('admin.login.import.preview');
    Route::post('/admin/login/import', [AdminLoginController::class, 'import'])->name('admin.login.import');
    Route::resource('/admin/login', AdminLoginController::class, ['names' => 'admin.login']);
});

// Siswa Routes
Route::middleware(['auth', 'is_siswa'])->group(function () {
    Route::get('/siswa/dashboard', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
    
    Route::get('/siswa/profile', [SiswaPklController::class, 'index'])->name('siswa.profile.index');
    Route::post('/siswa/profile/upload', [SiswaPklController::class, 'uploadBerkas'])->name('siswa.profile.upload-berkas');
    
    Route::resource('/siswa/dudi', SiswaDudiController::class, ['names' => 'siswa.dudi', 'only' => ['index']]);
    Route::post('/siswa/dudi/{dudi}/ajukan', [SiswaDudiController::class, 'ajukan'])->name('siswa.dudi.ajukan');
    
    Route::resource('/siswa/booking', SiswaBookingController::class, ['names' => 'siswa.booking', 'only' => ['index', 'show']]);
});

// Wali Kelas Routes
Route::middleware(['auth', 'is_wali_kelas'])->group(function () {
    Route::get('/wali-kelas/dashboard', [WaliKelasDashboardController::class, 'index'])->name('wali-kelas.dashboard');
    Route::get('/wali-kelas/siswas', [WaliKelasDashboardController::class, 'siswas'])->name('wali-kelas.siswas');
    Route::get('/wali-kelas/siswas/export/pdf', [App\Http\Controllers\PdfExportController::class, 'siswa'])->name('wali-kelas.siswas.export.pdf');
    Route::get('/wali-kelas/bookings', [WaliKelasDashboardController::class, 'bookings'])->name('wali-kelas.bookings');
});

// Kakonsli Routes
Route::middleware(['auth', 'is_kakonsli'])->group(function () {
    Route::get('/kakonsli/dashboard', [KakonslDashboardController::class, 'index'])->name('kakonsli.dashboard');
    Route::get('/kakonsli/siswas', [KakonslDashboardController::class, 'siswas'])->name('kakonsli.siswas');
    Route::get('/kakonsli/siswas/export/pdf', [App\Http\Controllers\PdfExportController::class, 'siswa'])->name('kakonsli.siswas.export.pdf');
    Route::get('/kakonsli/bookings', [KakonslDashboardController::class, 'bookings'])->name('kakonsli.bookings');
});

