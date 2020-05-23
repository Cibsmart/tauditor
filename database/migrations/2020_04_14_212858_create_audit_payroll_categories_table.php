<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditPayrollCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_payroll_categories', function (Blueprint $table) {
            $table->id();
            $table->string('payment_type_id');
            $table->string('payment_title')->index();
            $table->unsignedBigInteger('total_net_pay')->nullable();
            $table->unsignedBigInteger('head_count')->nullable();
            $table->unsignedBigInteger('audit_payroll_id');
            $table->timestamps();

            $table->foreign('payment_type_id')->references('id')->on('payment_types');
            $table->foreign('audit_payroll_id')->references('id')->on('audit_payrolls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_payroll_categories');
    }
}
