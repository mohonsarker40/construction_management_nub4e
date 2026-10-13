<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Backend\DashboardController;
use \App\Http\Controllers\Backend\LoginController;
use \App\Http\Controllers\FrontendController;


Route::prefix('/')->group(function () {
    Route::get('/', [FrontendController::class, 'index']);
});

Route::get('/login', [LoginController::class, 'index'])->name('login');
Route::post('/login', [LoginController::class, 'doLogin'])->name('doLogin');


Route::prefix('admin')->middleware('authCheck')->group(function () {
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');


   // Routes will be here...

});


