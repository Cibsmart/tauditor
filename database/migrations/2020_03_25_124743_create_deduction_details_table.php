<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_id');
            $table->unsignedBigInteger('deduction_id');
            $table->unsignedBigInteger('amount');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('beneficiary_id')->references('id')->on('beneficiaries');
            $table->foreign('deduction_id')->references('id')->on('deductions');

            $table->index(['deduction_id', 'beneficiary_id', 'deleted_at'], 'unique_beneficiary_deductions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deduction_details');
    }
}
