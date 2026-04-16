<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MasterDataController;
use App\Http\Controllers\ActivityController;

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

// Password Reset Routes
Route::get('password/reset', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
Route::post('password/email', [App\Http\Controllers\Auth\ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');
Route::get('password/reset/{token}', [App\Http\Controllers\Auth\ResetPasswordController::class, 'showResetForm'])->name('password.reset');
Route::post('password/reset', [App\Http\Controllers\Auth\ResetPasswordController::class, 'reset'])->name('password.update');

// Public Pages
Route::view('/developer', 'developer.index')->name('developer');

// Dashboard Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index']);
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/events', [DashboardController::class, 'getEvents'])->name('dashboard.events.get');
    Route::post('/dashboard/events', [DashboardController::class, 'store'])->name('dashboard.events.store');
    Route::put('/dashboard/events/{event}', [DashboardController::class, 'update'])->name('dashboard.events.update');
    Route::delete('/dashboard/events/{event}', [DashboardController::class, 'destroy'])->name('dashboard.events.destroy');
    
    // Unified Activities
    Route::get('/activities/past', [ActivityController::class, 'past'])->name('activities.past');
    Route::post('/activities/{activity}/upload-minutes', [ActivityController::class, 'uploadMinutes'])->name('activities.upload-minutes');
    Route::delete('/activities/{activity}/minutes', [ActivityController::class, 'deleteMinutes'])->name('activities.delete-minutes');
    Route::post('/activities/{activity}/moms', [ActivityController::class, 'uploadMom'])->name('activities.upload-mom');
    Route::delete('/activities/moms/{mom}', [ActivityController::class, 'deleteMom'])->name('activities.delete-mom');
    Route::post('/activities/{activity}/upload-assignment', [ActivityController::class, 'uploadAssignmentLetter'])->name('activities.upload-assignment');
    Route::delete('/activities/{activity}/assignment', [ActivityController::class, 'deleteAssignment'])->name('activities.delete-assignment');
    Route::put('/activities/{activity}/summary', [ActivityController::class, 'updateSummary'])->name('activities.update-summary');
    Route::put('/activities/{activity}/additional-notes', [ActivityController::class, 'updateAdditionalNotes'])->name('activities.update-additional-notes');
    
    // Materials & Documentation
    Route::delete('/activities/{activity}/attachment', [ActivityController::class, 'deleteAttachment'])->name('activities.delete-attachment');
    Route::post('/activities/{activity}/materials', [ActivityController::class, 'uploadMaterial'])->name('activities.upload-material');
    Route::delete('/activities/materials/{material}', [ActivityController::class, 'deleteMaterial'])->name('activities.delete-material');
    Route::post('/activities/{activity}/documentations', [ActivityController::class, 'uploadDocumentation'])->name('activities.upload-documentation');
    Route::delete('/activities/documentations/{documentation}', [ActivityController::class, 'deleteDocumentation'])->name('activities.delete-documentation');

    Route::resource('activities', ActivityController::class);

    // Follow-up Dashboard
    Route::get('/follow-up', function () {
        return view('followup.dashboard');
    })->name('followup.dashboard');

    // Profile Management
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');

    // Reporting
    Route::get('/report/h1', [\App\Http\Controllers\ReportController::class, 'index'])->name('report.h1');
    Route::get('/report/h1-visual', [\App\Http\Controllers\ReportController::class, 'visualH1'])->name('report.h1-visual');

    // Master Data Routes (Admin Only - checked in controller)
    Route::prefix('master-data')->name('master-data.')->group(function () {
        Route::get('/', [MasterDataController::class, 'index'])->name('index');
        Route::get('/create', [MasterDataController::class, 'create'])->name('create');
        Route::get('/{user}/edit', [MasterDataController::class, 'edit'])->name('edit');
        Route::get('/topics', [MasterDataController::class, 'topics'])->name('topics');
        Route::post('/reorder', [MasterDataController::class, 'reorder'])->name('reorder');
        Route::post('/', [MasterDataController::class, 'store'])->name('store');
        Route::put('/{user}', [MasterDataController::class, 'update'])->name('update');
        Route::delete('/{user}', [MasterDataController::class, 'destroy'])->name('destroy');
        
        // Master Staff Routes
        Route::resource('staff', \App\Http\Controllers\MasterStaffController::class)->except(['create', 'show', 'edit']);

        // Division Management
        Route::get('/divisions', [MasterDataController::class, 'divisions'])->name('divisions');
        Route::post('/divisions', [MasterDataController::class, 'storeDivision'])->name('divisions.store');
        Route::put('/divisions/{division}', [MasterDataController::class, 'updateDivision'])->name('divisions.update');
        Route::delete('/divisions/{division}', [MasterDataController::class, 'destroyDivision'])->name('divisions.destroy');
        Route::post('/divisions/reorder', [MasterDataController::class, 'reorderDivision'])->name('divisions.reorder');

        // Position Management
        Route::post('/positions', [MasterDataController::class, 'storePosition'])->name('positions.store');
        Route::put('/positions/{position}', [MasterDataController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/positions/{position}', [MasterDataController::class, 'destroyPosition'])->name('positions.destroy');
        Route::post('/positions/reorder', [MasterDataController::class, 'reorderPosition'])->name('positions.reorder');
    });
});
