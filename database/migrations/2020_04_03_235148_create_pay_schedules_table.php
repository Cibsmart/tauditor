<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_schedules', function (Blueprint $table) {
            $table->id();
            $table->string('beneficiary_code');
            $table->string('beneficiary_name');
            $table->string('account_number')->nullable();
            $table->string('bank_name');
            $table->unsignedBigInteger('net_pay');

            $table->unsignedBigInteger('basic_pay');
            $table->unsignedBigInteger('gross_pay');
            $table->unsignedBigInteger('total_allowance')->default(0);
            $table->unsignedBigInteger('total_deduction');
            $table->text('allowances');
            $table->text('deductions');

            $table->unsignedBigInteger('payroll_id');
            $table->unsignedBigInteger('beneficiary_id');

            $table->string('verification_number');
            $table->unsignedBigInteger('beneficiary_type_id');
            $table->string('bankable_type');
            $table->unsignedBigInteger('bankable_id');
            $table->unsignedBigInteger('mda_id');
            $table->unsignedBigInteger('sub_mda_id')->nullable();
            $table->unsignedBigInteger('sub_sub_mda_id')->nullable();

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
        Schema::dropIfExists('pay_schedules');
    }
}
