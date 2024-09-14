<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

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
    // Route::post('/users', [UserController::class, 'store'])->name('users.store');
    // Route::get('/users', [UserController::class, 'index'])->name('users.index');
    // Route::get('/users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');
    // Route::put('/users/update', [UserController::class, 'update'])->name('users.update');
    // Route::delete('/users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    // List users and handle AJAX Datatables requests
    // Route::get('users', [UserController::class, 'index'])->name('users.index');

    // // Create a new user
    // Route::post('users', [UserController::class, 'store'])->name('users.store');

    // // Fetch a user for editing
    // Route::get('users/{id}/edit', [UserController::class, 'edit'])->name('users.edit');

    // // Update an existing user
    // Route::put('users/{id}', [UserController::class, 'update'])->name('users.update');

    // // Delete a user
    // Route::delete('users/{id}', [UserController::class, 'destroy'])->name('users.destroy');

    Route::resource('users', UserController::class);
});

require __DIR__.'/auth.php';
