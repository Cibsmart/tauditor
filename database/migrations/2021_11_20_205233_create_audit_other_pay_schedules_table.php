<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditOtherPaySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_other_pay_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('other_audit_payroll_category_id');
            $table->unsignedInteger('serial_number');
            $table->string('beneficiary_name')->index();
            $table->string('narration');
            $table->unsignedBigInteger('amount');
            $table->string('account_number')->nullable();
            $table->string('bank_name')->index();
            $table->string('bank_code');
            $table->string('bankable_type')->nullable();
            $table->unsignedBigInteger('bankable_id')->nullable();
            $table->unsignedBigInteger('autopay_schedule_id')->nullable();
            $table->boolean('paid')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('other_audit_payroll_category_id', 'other_payroll_category_pay_schedule_indes')
                ->references('id')
                ->on('other_audit_payroll_categories')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_other_pay_schedules');
    }
}
