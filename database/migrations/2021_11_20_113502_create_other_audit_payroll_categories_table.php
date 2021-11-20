<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOtherAuditPayrollCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('other_audit_payroll_categories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_payroll_id');
            $table->string('payment_type_id');
            $table->string('payment_title')->index();
            $table->boolean('paycomm_tenece')->default(0);
            $table->boolean('paycomm_fidelity')->default(0);
            $table->unsignedBigInteger('total_net_pay')->nullable();
            $table->unsignedBigInteger('head_count')->nullable();
            $table->dateTime('uploaded')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

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
        Schema::dropIfExists('other_audit_payroll_categories');
    }
}
