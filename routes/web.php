<?php

use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Frontend\HomeSearchController;
use App\Http\Controllers\Frontend\MyListingsController;

// Homepage (Frontend)
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/search', [HomeSearchController::class, 'search'])->name('search.listings');

Route::get('/favorites', function () {
    return view('frontend.favorites');
})->name('favorites.page');

Route::get('/my-listings', [MyListingsController::class, 'index'])
    ->name('my.listings')
    ->middleware('auth');

// User profile (requires login)
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/verify-email', function () {
    return view('auth.pending-verification');
})->name('verify.notice');

Route::post('/verify-email/resend', [RegisteredUserController::class, 'resend'])
    ->name('verify.resend');

Route::get('/verify/{token}', [RegisteredUserController::class, 'verify'])
    ->name('verify.complete');

Route::put('/password', [\App\Http\Controllers\ProfileController::class, 'updatePassword'])
    ->name('password.update')
    ->middleware('auth');


require __DIR__.'/auth.php';