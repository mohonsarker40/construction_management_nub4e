<?php

use App\Http\Controllers\Backend\Booking\BookingWorkflowController;
use App\Http\Controllers\Backend\Client\ClientController;
use App\Http\Controllers\Backend\Client\ClientDashboardController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\LoginController;
use App\Http\Controllers\Backend\RBAC\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\FrontendController;
use Illuminate\Support\Facades\Route;

Route::get('/', [FrontendController::class, 'index']);

Route::controller(FrontendController::class)->group(function () {
    Route::get('/client-register', 'clientRegisterForm')->name('client.register.form');
    Route::post('/client-register', 'clientRegister')->middleware('throttle:5,1')->name('client.register');
    Route::get('/booking-request', 'bookingRequestForm')->name('booking.request.form');
    Route::post('/booking-request', 'submitBookingRequest')->middleware('throttle:10,1')->name('booking.request.submit');
    Route::get('/request-status', 'requestStatusForm')->name('booking.request.status.form');
    Route::post('/request-status', 'requestStatus')->middleware('throttle:10,1')->name('booking.request.status');
});

Route::controller(LoginController::class)->group(function () {
    Route::get('/login', 'index')->name('login');
    Route::post('/login', 'doLogin')->name('doLogin');
});

Route::get('/client/dashboard', [ClientDashboardController::class, 'index'])
    ->middleware('authCheck')->name('client.dashboard');

Route::prefix('admin')->middleware('authCheck')->group(function () {
    Route::get('/', fn () => redirect()->route('dashboard'));
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');
    Route::patch('/clients/registrations/{user}/approve', [ClientController::class, 'approveRegistration'])
        ->middleware('adminOnly')->name('admin.clients.registrations.approve');

    Route::get('/booking-requests', [BookingWorkflowController::class, 'index'])->name('booking-requests.index');
    Route::get('/projects', [BookingWorkflowController::class, 'projectIndex'])->name('projects.index');
    Route::post('/projects', [BookingWorkflowController::class, 'storeProject'])->name('projects.store');
    Route::patch('/projects/{project}/completion', [BookingWorkflowController::class, 'completeProject'])->name('projects.completion');
    Route::patch('/projects/{project}/manager', [BookingWorkflowController::class, 'assignManager'])->name('projects.manager');
    Route::patch('/projects/{project}/designer', [BookingWorkflowController::class, 'assignDesigner'])->name('projects.designer');
    Route::patch('/booking-requests/{bookingRequest}/manager-review', [BookingWorkflowController::class, 'reviewByManager'])->name('booking-requests.manager-review');
    Route::patch('/booking-requests/{bookingRequest}/accountant-review', [BookingWorkflowController::class, 'reviewByAccountant'])->name('booking-requests.accountant-review');
    Route::patch('/booking-payments/{payment}/approve', [BookingWorkflowController::class, 'approvePayment'])->name('booking-payments.approve');
    Route::post('/project-expenses', [BookingWorkflowController::class, 'storeExpense'])->name('project-expenses.store');
    Route::get('/reports/finance', [BookingWorkflowController::class, 'report'])->name('reports.finance');

    Route::name('admin.')->middleware('adminOnly')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::get('/clients', [ClientController::class, 'index'])->name('clients.index');
        Route::get('/clients/{client}/edit', [ClientController::class, 'edit'])->name('clients.edit');
        Route::put('/clients/{client}', [ClientController::class, 'update'])->name('clients.update');
        Route::delete('/clients/{client}', [ClientController::class, 'destroy'])->name('clients.destroy');
    });
});
