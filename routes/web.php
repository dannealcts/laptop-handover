<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\{
    ProfileController,
    LaptopInvController,
    LaptopRequestController,
    ReturnRequestController,
    AdminController,
    HandoverHistoryController
};

/*
|--------------------------------------------------------------------------
| 🌐 GENERAL ROUTES
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome'); // Or change to 'auth.login' if using Laravel Breeze
});

/*Route::get('/dashboard', function () {
    return view('dashboard'); // Keep default Breeze dashboard
})->middleware(['auth', 'verified'])->name('dashboard');*/

Route::get('/redirect-by-role', function () {
    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('staff.dashboard');
})->middleware(['auth', 'verified']);

/*
|--------------------------------------------------------------------------
| 👤 PROFILE MANAGEMENT
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 🔐 AUTH ROUTES
|--------------------------------------------------------------------------
*/

require __DIR__ . '/auth.php';

/*
|--------------------------------------------------------------------------
| 🛠️ ADMIN ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {

    
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');

    // Laptop Inventory
    Route::resource('laptops', LaptopInvController::class)->names('laptops');


    // Laptop Requests
    Route::get('/view-requests', [LaptopRequestController::class, 'adminIndex'])->name('view-requests');
    Route::patch('/laptop-requests/{request}/approve', [LaptopRequestController::class, 'approve'])->name('requests.approve');
    Route::patch('/laptop-requests/{request}/reject', [LaptopRequestController::class, 'reject'])->name('requests.reject');
    Route::get('/laptop-requests/{id}/assign', [LaptopRequestController::class, 'assignForm'])->name('assign-form');
    Route::post('/laptop-requests/{id}/assign', [LaptopRequestController::class, 'assignLaptop'])->name('assign-laptop');

    // Part/Upgrade Assignments
    Route::get('/requests/{id}/assign-part-upgrade', [LaptopRequestController::class, 'assignPartUpgradeForm'])->name('requests.assign-part-upgrade');
    Route::post('/requests/{id}/assign-part-upgrade', [LaptopRequestController::class, 'storeAssignedPartUpgrade'])->name('requests.assign-part-upgrade.submit');
    Route::patch('/requests/{id}/mark-completed', [LaptopRequestController::class, 'markAsCompleted'])->name('requests.mark-completed');

    // Return Requests
    Route::get('/return-requests', [ReturnRequestController::class, 'adminIndex'])->name('view-return-requests.index');
    Route::patch('/return-requests/{id}/mark-received', [ReturnRequestController::class, 'markAsReceived'])->name('view-return-requests.mark-received');
    Route::post('/return-requests/{id}/complete', [ReturnRequestController::class, 'complete'])->name('view-return-requests.complete');
    Route::delete('/return-request/{id}/delete', [ReturnRequestController::class, 'delete'])->name('return.delete');


    // History
    Route::get('/history', [AdminController::class, 'history'])->name('history');

    // Export Request
    Route::get('/export-request', [LaptopRequestController::class, 'exportForm'])->name('export-request.form');
    Route::post('/export-request/search', [LaptopRequestController::class, 'searchStaff'])->name('export-request.search');
    Route::post('/export-request/export/{userId}', [LaptopRequestController::class, 'exportToExcel'])->name('export-request.generate');
    Route::get('/export-requests', [LaptopRequestController::class, 'exportForm'])->name('export.form'); // New Line
    Route::post('/export-selected', [LaptopRequestController::class, 'exportSelected'])->name('export.selected');
    Route::post('/export-all', [LaptopRequestController::class, 'exportAllFiltered'])->name('export.all');


    // Optional redirect
    Route::get('/export-request-redirect', function () {
        return redirect()->route('admin.export-request.form');
    })->name('export-request');

    // Activities
    Route::get('/activities', [AdminController::class, 'viewActivities'])->name('activities');
});

/*
|--------------------------------------------------------------------------
| 👩‍💼 STAFF ROUTES
|--------------------------------------------------------------------------
*/

Route::prefix('staff')->middleware(['auth', 'verified'])->name('staff.')->group(function () {

    Route::get('/dashboard', function () {
        return view('staff.dashboard');
    })->name('dashboard');

    // Laptop Request
    Route::get('/make-request', [LaptopRequestController::class, 'create'])->name('make-request.create');
    Route::post('/make-request', [LaptopRequestController::class, 'store'])->name('make-request.store');

    // My Requests
    Route::get('/request-history', [HandoverHistoryController::class, 'myHistory'])->name('request-history');

    // Return Laptop
    Route::get('/return-laptop', [ReturnRequestController::class, 'create'])->name('return-laptop.create');
    Route::post('/return-laptop', [ReturnRequestController::class, 'store'])->name('return-laptop.store');
});
