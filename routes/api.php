<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoanDataController;
use App\Http\Controllers\Api\BeneficiaryController;
use App\Http\Controllers\CreateUserTokenController;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});





Route::middleware('auth:sanctum')->group(function () {
    Route::post('beneficiary/paye/month', [BeneficiaryController::class, 'payeMonth']);
    Route::post('beneficiary/paye/year', [BeneficiaryController::class, 'payeYear']);

    Route::get('beneficiary/{domain}/{verification_number}/{account_number}', [LoanDataController::class, 'index']);
//    Route::get('beneficiaries/{domain}/{year}/{month}/{type}', [BeneficiaryController::class, 'index']);

    Route::fallback([BeneficiaryController::class, 'invalid']);
});


