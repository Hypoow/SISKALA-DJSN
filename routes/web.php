<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

// Authentication Routes
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/events', [DashboardController::class, 'getEvents'])->name('dashboard.events.get');
    Route::post('/dashboard/events', [DashboardController::class, 'store'])->name('dashboard.events.store');
    Route::put('/dashboard/events/{event}', [DashboardController::class, 'update'])->name('dashboard.events.update');
    Route::delete('/dashboard/events/{event}', [DashboardController::class, 'destroy'])->name('dashboard.events.destroy');
    
    // Unified Activities
    Route::resource('activities', \App\Http\Controllers\ActivityController::class);

    // Master Data Routes (Admin Only - checked in controller)
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', [MasterDataController::class, 'index'])->name('index');
        Route::post('/', [MasterDataController::class, 'store'])->name('store');
        Route::put('/{user}', [MasterDataController::class, 'update'])->name('update');
        Route::delete('/{user}', [MasterDataController::class, 'destroy'])->name('destroy');
    });
});
