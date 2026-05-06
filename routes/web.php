<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipBillingController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Gebruiker can only access dashboard — all routes below require bestuur or admin
    Route::middleware('role:admin,bestuur')->group(function () {
        Route::resource('membership-types', MembershipTypeController::class)->except('show');
        Route::resource('members', MemberController::class);
        Route::resource('products', ProductController::class);
        Route::resource('events', EventController::class);
        Route::patch('event-tasks/{task}/status', [EventController::class, 'updateTaskStatus'])->name('event-tasks.status');

        Route::get('membership-billing', [MembershipBillingController::class, 'index'])->name('membership-billing.index');
        Route::post('membership-billing', [MembershipBillingController::class, 'store'])->name('membership-billing.store');

        Route::resource('invoices', InvoiceController::class);
        Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

        // Only admin can mark invoices as sent
        Route::patch('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])
            ->name('invoices.mark-sent')
            ->middleware('role:admin');
    });

    // User management — admin only
    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
    });
});
