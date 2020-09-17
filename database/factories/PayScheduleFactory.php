<?php

/** @var \Illuminate\Database\Eloquent\Factory $factory */

use App\Models\Payroll;
use App\Models\PaySchedule;
use App\Models\Beneficiary;
use Faker\Generator as Faker;
use Facades\Tests\Setup\BeneficiaryTestFactory;

$factory->define(PaySchedule::class, function (Faker $faker) {
    $b = BeneficiaryTestFactory::withAllowances($faker->numberBetween(5, 10))
                                       ->withDeductions($faker->numberBetween(5, 10))
                                       ->withStructuredSalary()
                                       ->withMda()
                                       ->withMfb()
                                       ->create();

    $gross = $b->basic() + $b->totalMonthlyAllowance();

    $net = $gross - $b->totalMonthlyDeduction();

    return [
        'beneficiary_code' => $b->accountNumber(),
        'beneficiary_name' => $b->name,
        'account_number' => $b->accountNumber(),
        'bank_name' => $b->bankName(),
        'net_pay' => $net,
        'basic_pay' => $b->basic(),
        'gross_pay' => $gross,
        'total_allowance' => $b->totalMonthlyAllowance(),
        'total_deduction' => $b->totalMonthlyDeduction(),
        'allowances' => $b->allowances(),
        'deductions' => $b->deductions(),
        'payroll_id' => factory(Payroll::class),
        'beneficiary_id' => $b->id,
        'verification_number' => $b->id,
        'beneficiary_type_id' => $b->beneficiary_type_id,
        'bankable_type' => $b->bankDetail->bankable_type,
        'bankable_id' => $b->bankDetail->bankable_id,
        'payable_type' => $b->salaryDetail->payable_type,
        'payable_id' => $b->salaryDetail->payable_id,
        'mda_id' => $b->mdaDetail->mda_id,
        'mda_name' => $b->mdaDetail->mda->name,
        'sub_mda_name' => $b->mdaDetail->subMda->name,
        'sub_sub_mda_name' => $b->mdaDetail->subSubMda->name,
        'pensioner' => 0,
    ];
});
