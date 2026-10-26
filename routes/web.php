<?php

use App\Http\Controllers\Backend\StaffController;
use App\Http\Controllers\Backend\SupplierController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\ClientController;
use \App\Http\Controllers\Backend\LoginController;
use \App\Http\Controllers\FrontendController;


Route::prefix('/')->group(function () {
    Route::get('/', [FrontendController::class, 'index']);
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'doLogin'])->name('doLogin');


Route::prefix('admin')->middleware('authCheck')->group(function () {
    Route::get('/', function () {
        return redirect()->route('dashboard');
    });
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

    Route::name('admin.')->middleware('adminOnly')->group(function () {
        Route::resource('roles', RoleController::class);
        Route::resource('users', UserController::class);
        Route::resource('clients', ClientController::class)->except(['show']);
        Route::resource('staffManagement', StaffController::class)->parameters(['staffManagement' => 'staff'])->except(['show']);
        Route::resource('suppliers', SupplierController::class)->parameters(['suppliers' => 'supplier'])->except(['show']);
    });

});


