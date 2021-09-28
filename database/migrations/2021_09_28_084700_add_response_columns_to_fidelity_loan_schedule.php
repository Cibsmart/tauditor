<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddResponseColumnsToFidelityLoanSchedule extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('fidelity_loan_schedules', function (Blueprint $table) {
            $table->json('response_data')->nullable()->after('confirmation_sent');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('fidelity_loan_schedules', function (Blueprint $table) {
            $table->dropColumn('response_data');
        });
    }
}
