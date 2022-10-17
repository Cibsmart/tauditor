<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeneficiaryTypeColumnToFidelityLoanDeductions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fidelity_loan_deductions', function (Blueprint $table) {
            $table->string('beneficiary_type_id')->after('fidelity_loan_schedule_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fidelity_loan_deductions', function (Blueprint $table) {
            $table->dropColumn(['beneficiary_type_id']);
        });
    }
}
