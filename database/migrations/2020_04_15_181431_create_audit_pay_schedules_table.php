<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditPaySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_pay_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_sub_mda_schedule_id');
            $table->string('verification_number');
            $table->string('beneficiary_name');
            $table->string('beneficiary_cadre');
            $table->string('designation');
            $table->unsignedBigInteger('basic_pay');
            $table->string('bank_name');
            $table->string('account_number');
            $table->string('bank_code');
            $table->unsignedBigInteger('total_allowance');
            $table->unsignedBigInteger('total_deduction');
            $table->string('gross_pay');
            $table->string('net_pay');
            $table->string('allowances');
            $table->string('deductions');
            $table->string('mda_name');
            $table->string('department_name');
            $table->string('month');
            $table->string('year');
            $table->string('reference_number')->nullable();
            $table->timestamp('autopay_upload')->nullable();
            $table->boolean('paid')->default(0);
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
        Schema::dropIfExists('audit_pay_schedules');
    }
}
