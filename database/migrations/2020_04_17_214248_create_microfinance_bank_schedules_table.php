<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicrofinanceBankSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('microfinance_bank_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_sub_mda_schedule_id');
            $table->unsignedBigInteger('micro_finance_bank_id');
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
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('microfinance_bank_schedules');
    }
}
