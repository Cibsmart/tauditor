<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBeneficiaryTypeColumnToMicrofinanceBankSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('microfinance_bank_schedules', function (Blueprint $table) {
            $table->string('beneficiary_type_id')->after('currency')
            ;
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('microfinance_bank_schedules', function (Blueprint $table) {
            $table->dropColumn(['beneficiary_type_id']);
        });
    }
}
