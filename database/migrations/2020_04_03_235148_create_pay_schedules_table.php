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
            $table->unsignedBigInteger('beneficiary_id');
            $table->string('account_number')->nullable();
            $table->string('bank_name');
            $table->unsignedBigInteger('net_pay');
            $table->unsignedBigInteger('basic_pay');
            $table->unsignedBigInteger('total_allowances')->default(0);
            $table->unsignedBigInteger('total_deductions')->default(0);
            $table->text('allowances')->nullable();
            $table->text('deductions')->nullable();
            $table->unsignedBigInteger('payroll_id');
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
        Schema::dropIfExists('pay_schedules');
    }
}
