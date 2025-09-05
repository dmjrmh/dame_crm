<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\CustomerController;

Route::get('/', function () {
  return view('welcome');
});

Route::get('/dashboard', function () {
  return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
  Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
  Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
  Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
  Route::middleware('role:manager')->group(function () {
    Route::get('/reports', fn() => 'Laporan semua sales');
  });

  Route::middleware('role:sales')->group(function () {
    Route::get('/leads', fn() => 'Daftar Leads');
  });

  Route::resource('leads', LeadController::class);
  Route::resource('products', ProductController::class);

  Route::middleware(['auth', 'role:manager'])->group(function () {
    Route::get('/projects/approvals', [ProjectController::class, 'approvals'])
      ->name('approvals');

    Route::put('/projects/{deal}/approve', [ProjectController::class, 'approve'])
      ->name('projects.approve');

    Route::put('/projects/{deal}/reject', [ProjectController::class, 'reject'])
      ->name('projects.reject');
  });
  Route::resource('projects', ProjectController::class);

  Route::resource('customers', CustomerController::class)->only(['index','show']);
});

require __DIR__ . '/auth.php';
