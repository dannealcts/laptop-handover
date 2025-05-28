<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

// Shared controllers
use App\Http\Controllers\ProfileController;

// 🛠️ Admin Controllers
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\LaptopController as LaptopInvController;
use App\Http\Controllers\Admin\LaptopRequestController;
use App\Http\Controllers\Admin\LaptopAssignmentController;
use App\Http\Controllers\Admin\ExportController;
use App\Http\Controllers\Admin\ReturnRequestController as AdminReturnController;
use App\Http\Controllers\Admin\HandoverHistoryController as AdminHandoverController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\HistoryController;

// 👩‍💼 Staff Controllers
use App\Http\Controllers\Staff\StaffController;
use App\Http\Controllers\Staff\LaptopRequestController as StaffLaptopRequestController;
use App\Http\Controllers\Staff\ReturnRequestController as StaffReturnController;
use App\Http\Controllers\Staff\HandoverHistoryController as StaffHandoverController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/redirect-by-role', function () {
    return Auth::user()->role === 'admin'
        ? redirect()->route('admin.dashboard')
        : redirect()->route('staff.dashboard');
})->middleware(['auth', 'verified']);

Route::get('/admin/test-upgrade-mail/{userId}', [NotificationController::class, 'sendUpgradeEmail'])->name('admin.test-upgrade-mail');


/*
|--------------------------------------------------------------------------
| 👤 Profile Management
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

/*
|--------------------------------------------------------------------------
| 🔐 Auth Routes
|--------------------------------------------------------------------------
*/
require __DIR__ . '/auth.php';

/* 
|--------------------------------------------------------------------------
| 🛠️ Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->middleware(['auth', 'verified'])->name('admin.')->group(function () {
    
    // Dashboard
    Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('dashboard');
    Route::post('/notify-upgrade/{userId}', [NotificationController::class, 'sendUpgradeEmail'])->name('notify-upgrade');

    // Laptop Inventory (This uses the correct resource routes)
    Route::resource('laptops', LaptopInvController::class)->names('laptops');

    // Staff Laptop Requests (This was incorrectly named as 'laptops.index' before)
    Route::get('/requests', [LaptopRequestController::class, 'index'])->name('requests.index');
    Route::post('/requests/{request}/approve', [LaptopRequestController::class, 'approve'])->name('requests.approve');
    Route::post('/requests/{request}/reject', [LaptopRequestController::class, 'reject'])->name('requests.reject');

    // Assignment
    Route::get('/requests/{id}/assign', [LaptopAssignmentController::class, 'assignForm'])->name('assign.form');
    Route::post('/requests/{id}/assign', [LaptopAssignmentController::class, 'assignLaptop'])->name('assign.laptop');

    Route::get('/requests/{id}/assign-part', [LaptopAssignmentController::class, 'assignPartUpgradeForm'])->name('assign.part.form');
    Route::post('/requests/{id}/assign-part', [LaptopAssignmentController::class, 'storeAssignedPartUpgrade'])->name('assign.part.store');

    // Return Requests
    Route::get('/return-requests', [AdminReturnController::class, 'index'])->name('return.index');
    Route::post('/return-requests/{id}/complete', [AdminReturnController::class, 'complete'])->name('return.complete');
    Route::delete('/return-requests/{id}', [AdminReturnController::class, 'delete'])->name('return.delete');

    // History
    Route::get('/history', [HistoryController::class, 'index'])->name('history');

    // Activities
    Route::get('/activities', [AdminController::class, 'viewActivities'])->name('activities');

    // Export
    Route::get('/export', [ExportController::class, 'exportForm'])->name('export.form');
    Route::post('/export/selected', [ExportController::class, 'exportSelected'])->name('export.selected');
    Route::post('/export/all', [ExportController::class, 'exportAllFiltered'])->name('export.all');
    Route::post('/export/staff/{userId}', [ExportController::class, 'exportToExcel'])->name('export.staff');
});


/*
|--------------------------------------------------------------------------
| 👩‍💼 Staff Routes
|--------------------------------------------------------------------------
*/
Route::prefix('staff')->middleware(['auth', 'verified'])->name('staff.')->group(function () {
    // Dashboard
    Route::get('/dashboard', [StaffController::class, 'dashboard'])->name('dashboard');

    // Requests
    Route::get('/requests', [StaffLaptopRequestController::class, 'index'])->name('requests.index');
    Route::get('/make-request', [StaffLaptopRequestController::class, 'create'])->name('requests.create');
    Route::post('/make-request', [StaffLaptopRequestController::class, 'store'])->name('requests.store');

    // Return Laptop
    Route::get('/return-laptop', [StaffReturnController::class, 'create'])->name('return.create');
    Route::post('/return-laptop', [StaffReturnController::class, 'store'])->name('return.store');

    // Handover History
    Route::get('/my-history', [StaffHandoverController::class, 'myHistory'])->name('history');
});
