<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ActivityLogController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\LoanController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReturnLogController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth', 'verified'])->group(function () {
    // Dashboard (Admin & User otomatis dibedakan di Controller)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Profile (Bawaan Breeze)
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Barang: Index dapat dilihat oleh semua role terotentikasi
    Route::get('/items', [ItemController::class, 'index'])->name('items.index');

    // Peminjaman: Index, Create, Store dapat diakses oleh Admin & User
    Route::resource('loans', LoanController::class)->only(['index', 'create', 'store']);

    // ==========================================
    // KHUSUS ROLE ADMIN
    // ==========================================
    Route::middleware('role:admin')->group(function () {
        // Manajemen User
        Route::get('/users/{user}/reset-password', [UserController::class, 'resetPasswordForm'])->name('users.reset-password.form');
        Route::patch('/users/{user}/reset-password', [UserController::class, 'resetPassword'])->name('users.reset-password');
        Route::resource('users', UserController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        // CRUD Kategori
        Route::resource('categories', CategoryController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

        // CRUD Barang (Create, Store, Edit, Update, Destroy)
        Route::resource('items', ItemController::class)->only(['create', 'store', 'edit', 'update', 'destroy']);

        // Hapus peminjaman
        Route::delete('/loans/{loan}', [LoanController::class, 'destroy'])->name('loans.destroy');

        Route::get('/return-logs', [ReturnLogController::class, 'index'])->name('return-logs.index');
        Route::get('/return-logs/create', [ReturnLogController::class, 'create'])->name('return-logs.create');
        Route::post('/return-logs', [ReturnLogController::class, 'store'])->name('return-logs.store');

        Route::get('/activity-logs', [ActivityLogController::class, 'index'])->name('activity-logs.index');
    });
});

require __DIR__.'/auth.php';
