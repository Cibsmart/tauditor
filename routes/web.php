<?php

use App\AuditMdaSchedule;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PayrollController;
use App\Http\Controllers\AllowancesController;
use App\Http\Controllers\DeductionsController;
use App\Http\Controllers\DeductiblesController;
use App\Http\Controllers\RunPayrollController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\PayScheduleController;
use App\Http\Controllers\InterswitchController;
use App\Http\Controllers\MdaSchedulesController;
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
| Beneficiary Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('beneficiaries.')->group(function () {
        Route::get('beneficiaries', [BeneficiaryController::class, 'index'])->name('index');
        Route::get('beneficiaries/create', [BeneficiaryController::class, 'create'])->name('create');
        Route::post('beneficiaries/store', [BeneficiaryController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| Allowances Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('allowances.')->group(function () {
        Route::get('allowances', [AllowancesController::class, 'index'])->name('index');
        Route::get('allowances/create', [AllowancesController::class, 'create'])->name('create');
        Route::post('allowances/store', [AllowancesController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| Deductions Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('deductions.')->group(function () {
        Route::get('deductions', [DeductionsController::class, 'index'])->name('index');
        Route::get('deductions/create', [DeductionsController::class, 'create'])->name('create');
        Route::post('deductions/store', [DeductionsController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| Deductibles Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('deductibles.')->group(function () {
        Route::get('deductibles/{deduction}/create', [DeductiblesController::class, 'create'])->name('create');
        Route::post('deductibles/store', [DeductiblesController::class, 'store'])->name('store');
    });
});



/*
|-------------------------------------------------------------------------------
| Payroll Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('payroll.')->group(function () {
        Route::get('payroll', [PayrollController::class, 'index'])->name('index');
        Route::get('payroll/create', [PayrollController::class, 'create'])->name('create');
        Route::post('payroll/store', [PayrollController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| MDA Pay Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('mda_schedules.')->group(function () {
        Route::get('payroll_mda_schedule/{payroll}/index', [MdaSchedulesController::class, 'index'])->name('index');
    });
});


/*
|-------------------------------------------------------------------------------
| Pay Schedule Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('pay_schedules.')->group(function () {
        Route::get('payroll_pay_schedule/{payroll}/mda/{mda}/index', [PayScheduleController::class, 'index'])->name('index');
        Route::post('payroll_pay_schedule/{payroll}/store', [PayScheduleController::class, 'store'])->name('store');
    });
});


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
        Route::get('audit_mda_schedules/{audit_payroll}/index', [AuditMdaScheduleController::class, 'index'])->name('index');
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
        Route::post('audit_autopay/{audit_payroll}/generate', [AuditAutopayController::class, 'generate'])->name('generate');
        Route::get('audit_autopay/{audit_payroll}/download', [AuditAutopayController::class, 'download'])->name('download');
        Route::get('audit_autopay/{audit_payroll}/downloadMfb', [AuditAutopayController::class, 'downloadMfb'])->name('downloadMfb');
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
        Route::get('audit_analysis/{audit_payroll}/show', [AuditAnalysisController::class, 'show'])->name('show');
        Route::post('audit_analysis/{audit_payroll}/analyse', [AuditAnalysisController::class, 'analyse'])->name('analyse');
    });
});



/*
|-------------------------------------------------------------------------------
| Interswitch Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('interswitch.')->group(function () {
        Route::post('interswitch/{audit_payroll}/process', [InterswitchController::class, 'process'])->name('process');
    });
});




/*
|-------------------------------------------------------------------------------
| Invokable Controller Routes (Controllers that perform has one method)
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('actions.')->group(function () {
        Route::post('payroll/{payroll}/run_payroll', RunPayrollController::class)->name('run_payroll');
    });
});
