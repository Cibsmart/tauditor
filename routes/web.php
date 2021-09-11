<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReportsController;
use App\Http\Controllers\WelcomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\TmsPayeApiController;
use App\Http\Controllers\ManageUserController;
use App\Http\Controllers\InterswitchController;
use App\Http\Controllers\MfbScheduleController;
use App\Http\Controllers\BeneficiaryController;
use App\Http\Controllers\AuditPayrollController;
use App\Http\Controllers\AuditAutopayController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\AuditAnalysisController;
use App\Http\Controllers\CreateUserTokenController;
use App\Http\Controllers\FidelityMandateController;
use App\Http\Controllers\AuditPayScheduleController;
use App\Http\Controllers\AuditMdaScheduleController;
use App\Http\Controllers\AuditSubMdaSchedulesController;
use App\Http\Controllers\DeductionConfirmationController;

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

Route::get('/', [WelcomeController::class, 'index'])
     ->middleware('guest')
     ->name('welcome');

Route::prefix('{domain}')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])
         ->middleware('guest')
         ->name('login');
});

Route::get(
    'register',
    [RegisterController::class, 'showRegistrationForm']
)->middleware('guest')->name('register.form');

Route::get('dashboard', [DashboardController::class, 'index'])->middleware('auth')->name('dashboard');
Route::post('login', [LoginController::class, 'login'])->middleware('guest')->name('login.attempt');
Route::post('logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');
Route::post('register', [RegisterController::class, 'register'])->middleware('guest')->name('register.store');

/*
|-------------------------------------------------------------------------------
| Beneficiaries Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('beneficiaries.')->group(function () {
        Route::get('upload/beneficiaries_profile', [BeneficiaryController::class, 'create'])->name('create');
        Route::post('upload/beneficiaries_profile', [BeneficiaryController::class, 'upload'])->name('upload');
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
        Route::get(
            'audit_payroll/{audit_payroll}/leave_allowance',
            [AuditPayrollController::class, 'leave']
        )->name('leave');
    });
});


/*
|-------------------------------------------------------------------------------
| Audit MDA Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_mda_schedules.')->group(function () {
        Route::get(
            'audit_mda_schedules/{audit_payroll_category}/index',
            [AuditMdaScheduleController::class, 'index']
        )->name('index');
    });
});

/*
|-------------------------------------------------------------------------------
| Audit Sub MDA Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_sub_mda_schedules.')->group(function () {
        Route::get(
            'audit_sub_mda_schedules/{audit_mda_schedule}/index',
            [AuditSubMdaSchedulesController::class, 'index']
        )->name('index');
    });
});


/*
|-------------------------------------------------------------------------------
| Audit Pay Schedules Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('audit_pay_schedules.')->group(function () {
        Route::get(
            'audit_pay_schedules/{audit_sub_mda_schedule}/index',
            [AuditPayScheduleController::class, 'index']
        )->name('index');
        Route::post(
            'audit_pay_schedules/{audit_sub_mda_schedule}/destroy',
            [AuditPayScheduleController::class, 'destroy']
        )->name('destroy');
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
        Route::get(
            'audit_autopay/{audit_payroll_category}/show',
            [AuditAutopayController::class, 'show']
        )->name('show');
        Route::get(
            'audit_autopay/{audit_mda_schedule}/detail',
            [AuditAutopayController::class, 'detail']
        )->name('detail');

        Route::post(
            'audit_autopay/{audit_payroll_category}/generate',
            [AuditAutopayController::class, 'generate']
        )->name('generate');
        Route::get(
            'audit_autopay/{audit_payroll_category}/download',
            [AuditAutopayController::class, 'download']
        )->name('download');
        Route::get(
            'audit_autopay/{audit_payroll_category}/downloadMfb',
            [AuditAutopayController::class, 'downloadMfb']
        )->name('downloadMfb');
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
        Route::get(
            'audit_analysis/{audit_payroll_category}/show',
            [AuditAnalysisController::class, 'show']
        )->name('show');
        Route::get(
            'audit_analysis/{audit_payroll_category}/report',
            [AuditAnalysisController::class, 'pdfReport']
        )->name('pdf_report');
        Route::post(
            'audit_analysis/{audit_payroll_category}/analyse',
            [AuditAnalysisController::class, 'analyse']
        )->name('analyse');
    });
});


/*
|-------------------------------------------------------------------------------
| Interswitch Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('interswitch.')->group(function () {
        Route::post(
            'interswitch/process_autopay_upload',
            [InterswitchController::class, 'process']
        )->name('process');
    });
});

/*
|-------------------------------------------------------------------------------
| TMS PAYE API Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('paye.')->group(function () {
        Route::get('paye/data', [TmsPayeApiController::class, 'index'])->name('index');
        Route::get('paye/data/{category}', [TmsPayeApiController::class, 'upload'])->name('upload');
    });
});


/*
|-------------------------------------------------------------------------------
| Report Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('reports.')->group(function () {
        Route::get('report/summary_report', [ReportsController::class, 'summaryView'])->name('summary_view');
        Route::post('report/summary_report', [ReportsController::class, 'summaryView'])->name('summary_post');
        Route::get(
            'report/{payroll}/summary_report_pdf',
            [ReportsController::class, 'summaryPrint']
        )->name('summary_print');

        Route::get('report/mda_report', [ReportsController::class, 'mdaView'])->name('mda_view');
        Route::post('report/mda_report', [ReportsController::class, 'mdaView'])->name('mda_post');
        Route::get('report/{category}/mda_report_pdf', [ReportsController::class, 'mdaPrint'])->name('mda_print');
    });
});

/*
|-------------------------------------------------------------------------------
| Manage Users Routes
|-------------------------------------------------------------------------------
*/
Route::group(['middleware' => ['auth', 'can:view_users']], function () {
    Route::name('manage_users.')->group(function () {
        Route::get('manage_users', [ManageUserController::class, 'index'])->name('index');
        Route::get('manage_users/create', [ManageUserController::class, 'create'])->name('create');
        Route::post('manage_users/store', [ManageUserController::class, 'store'])->name('store');
    });
});


/*
|-------------------------------------------------------------------------------
| MFB Schedule Routes
|-------------------------------------------------------------------------------
*/
Route::middleware(['auth', 'can:view_mfb_schedule'])->group(function () {
    Route::name('mfb_schedule.')->group(function () {
        Route::prefix('microfinance_bank_schedule')->group(function () {
            Route::get('', [MfbScheduleController::class, 'index'])->name('index');

            Route::get(
                '{category}/{mfb}/show',
                [MfbScheduleController::class, 'show']
            )->name('show');

            Route::get(
                'audit_autopay/{audit_mda_schedule}/detail',
                [AuditAutopayController::class, 'detail']
            )->name('detail');

            Route::get(
                '{category}/{mfb}/download',
                [MfbScheduleController::class, 'download']
            )->name('download');
        });
    });
});

/*
|-------------------------------------------------------------------------------
| Fidelity Bank Loan Management Routes
|-------------------------------------------------------------------------------
*/

Route::middleware(['auth'])->group(function () {
    Route::name('fidelity.')->group(function () {
        Route::prefix('fidelity')->group(function () {
            Route::get('mandate', [FidelityMandateController::class, 'index'])->name('index');
            Route::post('mandate', [FidelityMandateController::class, 'store'])->name('store');
            Route::get('mandate/{mandate}', [FidelityMandateController::class, 'show'])->name('show');

            Route::get(
                'beneficiary/deduction_confirmation',
                [DeductionConfirmationController::class, 'send']
            );
        });
    });
});


/*
|-------------------------------------------------------------------------------
| API Token Routes
|-------------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    Route::name('token.')->group(function () {
        Route::get('users/token', [CreateUserTokenController::class, 'create'])->name('token');
    });
});
