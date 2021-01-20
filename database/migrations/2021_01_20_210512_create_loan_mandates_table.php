<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanMandatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('loan_mandates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('beneficiary_id')->constrained();
            $table->string('reference');
            $table->string('staff_id');
            $table->string('bvn');
            $table->string('account_number');
            $table->string('phone_number');
            $table->string('currency');
            $table->integer('loan_amount');
            $table->integer('collection_amount');
            $table->integer('total_collection_amount');
            $table->integer('number_of_repayments');
            $table->dateTime('disbursement_date');
            $table->dateTime('collection_date');
            $table->string('authorization_code');
            $table->string('authorization_channel');
            $table->string('status');
            $table->timestamps();
            $table->softDeletes();

            $table->index('reference');
            $table->foreign('status')->references('id')->on('loan_statuses');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('loan_mandates');
    }
}
