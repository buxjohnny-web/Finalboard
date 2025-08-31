<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DriverController;
use App\Http\Controllers\CalculationController;
use App\Http\Controllers\PaymentController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

// Language switch
Route::get('/lang/{locale}', function ($locale) {
    if (in_array($locale, config('app.available_locales', ['en', 'fr']))) {
        session(['locale' => $locale]);
        cookie()->queue(cookie('locale', $locale, env('COOKIE_LOCALE_DURATION', 60 * 24 * 30)));
    }
    return redirect()->back();
})->name('lang.switch');

// Authentication
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Auth + Localization)
|--------------------------------------------------------------------------
*/
Route::middleware(['web', 'auth', \App\Http\Middleware\Localization::class])->group(function () {

    // Dashboard / Home
    Route::get('/', fn () => view('index'))->name('home');

    /*
    |--------------------------------------------------------------------------
    | Driver Management
    |--------------------------------------------------------------------------
    */
    Route::get('/drivers', [DriverController::class, 'index'])->name('drivers');
    Route::get('/drivers/{id}', [DriverController::class, 'show'])->name('drivers.show');
    Route::get('/newdriver', fn () => view('newdriver'))->name('newdriver');
    Route::post('/drivers/add', [DriverController::class, 'store'])->name('drivers.store');
    Route::get('/drivers/{id}/edit', [DriverController::class, 'edit'])->name('drivers.edit');
    Route::put('/drivers/{id}', [DriverController::class, 'update'])->name('drivers.update');
    Route::delete('/drivers/{id}', [DriverController::class, 'destroy'])->name('drivers.delete');

    /*
    |--------------------------------------------------------------------------
    | Calculations
    |--------------------------------------------------------------------------
    */
    // View calculations (multiple route formats for compatibility)
    Route::get('/calculate/{driver}/{week}', [CalculationController::class, 'show'])->name('calculate.week');
    Route::get('/drivers/{driver}/calculate/{week}', [CalculationController::class, 'show'])->name('calculate.show');

    // Upload & save
    Route::post('/calculate/upload', [CalculationController::class, 'uploadPdf'])->name('calculate.upload');
    Route::post('/calculate/save', [CalculationController::class, 'save'])->name('calculate.save');

    // Reset calculation (AJAX)
    Route::delete('/drivers/{driver}/calculate/{week}/reset', [CalculationController::class, 'reset'])->name('calculate.reset');

    // Edit & update calculation
    Route::get('/drivers/{driver}/calculate/{week}/edit', [CalculationController::class, 'edit'])->name('calculate.edit');
    Route::put('/drivers/{driver}/calculate/{week}', [CalculationController::class, 'update'])->name('calculate.update');

    /*
    |--------------------------------------------------------------------------
    | Payment Details
    |--------------------------------------------------------------------------
    */
    Route::get('paydetails/{driver}/{week}', [PaymentController::class, 'show'])->name('paydetails.show');

    // Home layout test pages
Route::get('/home/a', fn () => view('home_option_a'))->name('home.a');
Route::get('/home/b', fn () => view('home_option_b'))->name('home.b');
Route::get('/home/c', fn () => view('home_option_c'))->name('home.c');
});