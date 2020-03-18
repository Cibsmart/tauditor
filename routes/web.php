<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');

Route::get('login', [LoginController::class, 'showLoginForm'])->middleware('guest')->name('login');
Route::post('login', [LoginController::class, 'login'])->middleware('guest')->name('login.attempt');
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

Route::get('register', [RegisterController::class, 'showRegistrationForm'])->middleware('guest')->name('register.form');
Route::post('register', [RegisterController::class, 'register'])->middleware('guest')->name('register.store');



/*
|-------------------------------------------------------------------------------
| Employee Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('employees.')->group(function () {
        Route::get('employees', [EmployeeController::class, 'index'])->name('personal.index');
    });
});
