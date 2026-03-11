<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PostController;
use App\Http\Controllers\FollowerController;

Route::get('/', [PostController::class, 'index'])->middleware(['auth']);
Route::post('/posts', [PostController::class, 'store'])->middleware(['auth']);
Route::post('/follow/{user}', [FollowerController::class, 'follow'])->middleware(['auth']);
Route::post('/unfollow/{user}', [FollowerController::class, 'unfollow'])->middleware(['auth']);


// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
