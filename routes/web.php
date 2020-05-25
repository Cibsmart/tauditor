<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\InterswitchController;
use App\Http\Controllers\AuditPayrollController;
use App\Http\Controllers\AuditAutopayController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuditAnalysisController;
use App\Http\Controllers\AuditPayScheduleController;
use App\Http\Controllers\AuditMdaScheduleController;
use App\Http\Controllers\AuditSubMdaSchedulesController;

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
| Audit Payroll Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_payroll.')->group(function () {
        Route::get('audit_payroll', [AuditPayrollController::class, 'index'])->name('index');
        Route::post('audit_payroll/store', [AuditPayrollController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| Audit MDA Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_mda_schedules.')->group(function () {
        Route::get('audit_mda_schedules/{audit_payroll_category}/index', [AuditMdaScheduleController::class, 'index'])->name('index');
    });
});

/*
|-------------------------------------------------------------------------------
| Audit Sub MDA Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_sub_mda_schedules.')->group(function () {
        Route::get('audit_sub_mda_schedules/{audit_mda_schedule}/index', [AuditSubMdaSchedulesController::class, 'index'])->name('index');
    });
});


/*
|-------------------------------------------------------------------------------
| Audit Pay Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_pay_schedules.')->group(function () {
        Route::get('audit_pay_schedules/{audit_sub_mda_schedule}/index', [AuditPayScheduleController::class, 'index'])->name('index');
        Route::post('audit_pay_schedules/store', [AuditPayScheduleController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| Audit Autopay Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_autopay.')->group(function () {
        Route::get('audit_autopay', [AuditAutopayController::class, 'index'])->name('index');
        Route::get('audit_autopay/{audit_payroll_category}/show', [AuditAutopayController::class, 'show'])->name('show');
        Route::get('audit_autopay/{audit_mda_schedule}/detail', [AuditAutopayController::class, 'detail'])->name('detail');

        Route::post('audit_autopay/{audit_payroll_category}/generate', [AuditAutopayController::class, 'generate'])->name('generate');
        Route::get('audit_autopay/{audit_payroll_category}/download', [AuditAutopayController::class, 'download'])->name('download');
        Route::get('audit_autopay/{audit_payroll_category}/downloadMfb', [AuditAutopayController::class, 'downloadMfb'])->name('downloadMfb');
    });
});

/*
|-------------------------------------------------------------------------------
| Audit Analysis Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_analysis.')->group(function () {
        Route::get('audit_analysis', [AuditAnalysisController::class, 'index'])->name('index');
        Route::get('audit_analysis/{audit_payroll_category}/show', [AuditAnalysisController::class, 'show'])->name('show');
        Route::post('audit_analysis/{audit_payroll_category}/analyse', [AuditAnalysisController::class, 'analyse'])->name('analyse');
    });
});


/*
|-------------------------------------------------------------------------------
| Interswitch Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('interswitch.')->group(function () {
        Route::post('interswitch/process_autopay_upload', [InterswitchController::class, 'process'])->name('process');
    });
});
