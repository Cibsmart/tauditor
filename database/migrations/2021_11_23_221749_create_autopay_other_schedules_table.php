<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAutopayOtherSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('autopay_other_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('other_audit_payroll_category_id');
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
            $table->softDeletes();

            $table->foreign('other_audit_payroll_category_id')->references('id')->on('other_audit_payroll_categories');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('autopay_other_schedules');
    }
}
