<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFidelityLoanSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fidelity_loan_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_sub_mda_schedule_id');
            $table->string('payment_reference');
            $table->string('beneficiary_code');
            $table->string('beneficiary_name');
            $table->string('account_number');
            $table->string('account_type');
            $table->string('cbn_code');
            $table->string('is_cash_card');
            $table->string('narration');
            $table->string('amount');
            $table->string('email');
            $table->string('currency');
            $table->dateTime('confirmation_sent')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('fidelity_loan_schedules');
    }
}
