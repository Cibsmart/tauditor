<?php

use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\Api\CancelLoanMandateController;
use App\Http\Controllers\Api\LoanMandateController;
use App\Http\Controllers\Api\PaymentHistoryController;
use App\Http\Controllers\Api\SalaryHistoryController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
// });

Route::middleware('auth:sanctum')->group(function () {
    Route::prefix('beneficiary/paye')->group(function () {
        Route::post('month', [BeneficiaryController::class, 'payeMonth']);
        Route::post('year', [BeneficiaryController::class, 'payeYear']);
        Route::fallback([BeneficiaryController::class, 'invalid']);
    });

    Route::prefix('beneficiary/payment_history')->group(function () {
        Route::post('', [PaymentHistoryController::class, 'index']);
        Route::fallback([PaymentHistoryController::class, 'invalid']);
    });

    // Fidelity Loan Management APIs
    Route::prefix('beneficiary/salary_history')->group(function () {
        Route::post('', [SalaryHistoryController::class, 'show']);
        Route::fallback([SalaryHistoryController::class, 'invalid']);
    });

    Route::prefix('beneficiary/loan_mandate')->group(function () {
        Route::post('', [LoanMandateController::class, 'create']);
    });

    Route::prefix('beneficiary/cancel_mandate')->group(function () {
        Route::post('', [CancelLoanMandateController::class, 'create']);
    });
});
