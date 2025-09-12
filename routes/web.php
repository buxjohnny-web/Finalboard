<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Language Switch Route
|--------------------------------------------------------------------------
| This route is handled separately and stays outside the main groups.
*/
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, config('app.available_locales', ['en', 'fr']))) {
        session(['locale' => $locale]);
        cookie()->queue(cookie('locale', $locale, env('COOKIE_LOCALE_DURATION', 60 * 24 * 30)));
    }
    return redirect()->back();
})->name('lang.switch');


/*
|--------------------------------------------------------------------------
| Main Application Routes
|--------------------------------------------------------------------------
| These routes are grouped by authentication and localization middleware.
*/
Route::middleware(['web', \App\Http\Middleware\Localization::class])->group(function () {

    // --- Public Routes ---
    Route::controller(AuthController::class)->group(function () {
        Route::get('/login', 'showLogin')->name('login');
        Route::post('/login', 'login');
        Route::get('/register', 'showRegister')->name('register');
        Route::post('/register', 'register');
        Route::get('/auth/google/redirect', 'redirectToGoogle')->name('auth.google.redirect');
        Route::get('/auth/google/callback', 'handleGoogleCallback')->name('auth.google.callback');
        Route::get('/register/phone', 'showPhoneNumberForm')->name('register.phone');
        Route::post('/register/phone', 'storePhoneNumber')->name('register.phone.store');
    });

    Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');


    // --- Protected Routes (Requires Auth) ---
    Route::middleware('auth')->group(function () {

        // Home
        Route::get('/', fn () => view('index'))->name('home');

        // Driver Management Routes
        Route::controller(DriverController::class)->group(function () {
            Route::get('/drivers', 'index')->name('drivers');
            Route::get('/drivers/{id}', 'show')->name('drivers.show');
            Route::get('/newdriver', fn () => view('newdriver'))->name('newdriver');
            Route::post('/drivers/add', 'store')->name('drivers.store');
            Route::get('/drivers/{id}/edit', 'edit')->name('drivers.edit');
            Route::put('/drivers/{id}', 'update')->name('drivers.update');
            Route::delete('/drivers/{id}', 'destroy')->name('drivers.delete');
            Route::post('/drivers/{driver}/toggle-active', 'toggleActive')->name('drivers.toggleActive');
        });

        // Calculations Routes
        Route::controller(CalculationController::class)->group(function () {
            Route::get('/calculate/{driver}/{week}', 'show')->name('calculate.week');
            Route::get('/drivers/{driver}/calculate/{week}', 'show')->name('calculate.show');
            Route::post('/calculate/upload', 'uploadPdf')->name('calculate.upload');
            Route::post('/calculate/save', 'save')->name('calculate.save');
            Route::delete('/drivers/{driver}/calculate/{week}/reset', 'reset')->name('calculate.reset');
            Route::get('/drivers/{driver}/calculate/{week}/edit', 'edit')->name('calculate.edit');
            Route::put('/drivers/{driver}/calculate/{week}', 'update')->name('calculate.update');
        });
        
        // Payment Management Routes
        Route::controller(PaymentController::class)->group(function () {
            Route::get('/payments', 'index')->name('payments.index');
            Route::post('/payments/upload', 'batchUpload')->name('payments.upload');
            Route::get('paydetails/{driver}/{week}', 'show')->name('paydetails.show');
        });
    });
});