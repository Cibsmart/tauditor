<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFidelityLoanDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('fidelity_loan_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('loan_mandate_id')->constrained();
            $table->foreignId('audit_sub_mda_schedule_id')->constrained()->onDelete('cascade');
            $table->foreignId('fidelity_loan_schedule_id')->nullable();
            $table->unsignedBigInteger('amount');
            $table->string('loan_account');
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
        Schema::dropIfExists('fidelity_loan_deductions');
    }
}
