<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CalculationController;

// Language switch route (public)
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, config('app.available_locales', ['en', 'fr']))) {
        session(['locale' => $locale]);
        cookie()->queue(cookie('locale', $locale, env('COOKIE_LOCALE_DURATION', 60 * 24 * 30)));
    }
    return redirect()->back();
})->name('lang.switch');

// Public login routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Protected routes (only accessible if logged in)
Route::middleware(['web', 'auth', \App\Http\Middleware\Localization::class])->group(function () {
    Route::get('/', function () {
        return view('index');
    })->name('home');

    // Drivers
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers');
    Route::get('/drivers/{id}', [DriverController::class, 'show'])->name('drivers.show');
    Route::get('/newdriver', function () { return view('newdriver'); })->name('newdriver');
    Route::post('/drivers/add', [DriverController::class, 'store'])->name('drivers.store');
    Route::get('/drivers/{id}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('/drivers/{id}', [DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('drivers.delete');

    // Calculation (consistent names used in the Blade)
    Route::get('/drivers/{driver}/calculate/{week}', [CalculationController::class, 'show'])->name('calculate.show');
    Route::post('/calculate/upload', [CalculationController::class, 'uploadPdf'])->name('calculate.upload');
    Route::post('/calculate/save', [CalculationController::class, 'save'])->name('calculate.save');
});