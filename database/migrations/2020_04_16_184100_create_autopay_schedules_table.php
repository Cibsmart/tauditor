<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutopaySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autopay_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_sub_mda_schedule_id');
            $table->string('payment_reference');
            $table->string('beneficiary_code')->index();
            $table->string('beneficiary_name')->index();
            $table->string('account_number')->index();
            $table->string('account_type');
            $table->string('cbn_code');
            $table->string('is_cash_card');
            $table->string('narration');
            $table->string('amount');
            $table->string('email');
            $table->string('currency');
            $table->timestamps();

            $table->foreign('audit_sub_mda_schedule_id')->references('id')->on('audit_sub_mda_schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autopay_schedules');
    }
}
