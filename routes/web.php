<?php

use App\Http\Middleware\EnsureBuyer;
use App\Livewire\Buyer;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// ── Public (no login) ─────────────────────
Route::get('/track/{code?}', Buyer\TrackOrder::class)->name('buyer.track');

// ── Guest only ────────────────────────────
Route::middleware('guest')->group(function () {
    Route::get('/register', Buyer\Register::class)->name('buyer.register');
    Route::get('/login', Buyer\Login::class)->name('login');
});

// ── Buyer authenticated ───────────────────
Route::middleware(['auth', EnsureBuyer::class])->group(function () {
    Route::get('/dashboard', Buyer\Dashboard::class)->name('buyer.dashboard');
    Route::get('/orders', Buyer\OrderHistory::class)->name('buyer.orders');
    Route::get('/addresses', Buyer\Addresses::class)->name('buyer.addresses');
    Route::get('/profile', Buyer\Profile::class)->name('buyer.profile');

    Route::post('/logout', function () {
        Auth::logout();
        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    })->name('buyer.logout');
});
