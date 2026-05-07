<?php

use App\Http\Controllers\AclController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ImpersonateController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\MeetingController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\InvoiceController;
use App\Http\Controllers\LeadController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\MembershipBillingController;
use App\Http\Controllers\MembershipTypeController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('/login', [LoginController::class, 'login'])->middleware('guest');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout')->middleware('auth');

Route::middleware('auth')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::middleware('role:admin,bestuur')->group(function () {
        Route::resource('membership-types', MembershipTypeController::class)->except('show');
        Route::resource('members', MemberController::class);
        Route::get('members-export', [MemberController::class, 'export'])->name('members.export');
        Route::post('members-import', [MemberController::class, 'import'])->name('members.import');
        Route::resource('products', ProductController::class);
        Route::resource('events', EventController::class);
        Route::patch('event-tasks/{task}/status', [EventController::class, 'updateTaskStatus'])->name('event-tasks.status');
        Route::post('event-costs/{cost}/receipt', [EventController::class, 'uploadReceipt'])->name('event-costs.receipt');
        Route::delete('event-costs/{cost}/receipt', [EventController::class, 'deleteReceipt'])->name('event-costs.receipt.delete');
        Route::post('events/{event}/mail-declaratie', [EventController::class, 'mailDeclaratie'])->name('events.mail-declaratie');

        Route::resource('meetings', MeetingController::class);
        Route::post('meetings/{meeting}/notes', [MeetingController::class, 'saveNote'])->name('meetings.notes.save');
        Route::delete('meetings/{meeting}/notes', [MeetingController::class, 'deleteNote'])->name('meetings.notes.delete');
        Route::post('meetings/{meeting}/tasks', [MeetingController::class, 'storeTask'])->name('meetings.tasks.store');

        Route::get('membership-billing', [MembershipBillingController::class, 'index'])->name('membership-billing.index');
        Route::post('membership-billing', [MembershipBillingController::class, 'store'])->name('membership-billing.store');

        Route::resource('invoices', InvoiceController::class);
        Route::patch('invoices/{invoice}/mark-paid', [InvoiceController::class, 'markPaid'])->name('invoices.mark-paid');
        Route::get('invoices/{invoice}/pdf', [InvoiceController::class, 'pdf'])->name('invoices.pdf');

        Route::patch('invoices/{invoice}/mark-sent', [InvoiceController::class, 'markSent'])
            ->name('invoices.mark-sent')
            ->middleware('role:admin');

        // Leads
        Route::resource('leads', LeadController::class);
        Route::get('leads/{lead}/convert',  [LeadController::class, 'convertForm'])->name('leads.convert-form');
        Route::post('leads/{lead}/convert', [LeadController::class, 'convert'])->name('leads.convert');
    });

    Route::middleware('role:admin')->group(function () {
        Route::resource('users', UserController::class)->except('show');
        Route::post('users/{user}/send-welcome', [UserController::class, 'sendWelcome'])->name('users.send-welcome');
        Route::post('users/{user}/impersonate', [ImpersonateController::class, 'start'])->name('users.impersonate');
        Route::get('settings',  [SettingController::class, 'edit'])->name('settings.edit');
        Route::put('settings',  [SettingController::class, 'update'])->name('settings.update');
        Route::get('acl',  [AclController::class, 'edit'])->name('acl.edit');
        Route::put('acl',  [AclController::class, 'update'])->name('acl.update');
    });

    Route::resource('tasks', TaskController::class)->except('show');
    Route::patch('tasks/{task}/status', [TaskController::class, 'updateStatus'])->name('tasks.status');
    Route::patch('tasks/{task}/priority', [TaskController::class, 'updatePriority'])->name('tasks.priority');

    Route::post('impersonate/stop', [ImpersonateController::class, 'stop'])->name('impersonate.stop')->middleware('auth');

    Route::get('handleiding', fn () => view('handleiding'))->name('handleiding');
});
