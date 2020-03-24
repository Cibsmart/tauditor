<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowanceDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_id');
            $table->unsignedBigInteger('allowance_id');
            $table->unsignedBigInteger('amount');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('beneficiary_id')->references('id')->on('beneficiaries');
            $table->foreign('allowance_id')->references('id')->on('allowances');

            $table->index(['allowance_id', 'beneficiary_id', 'deleted_at'], 'unique_beneficiary_allowances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowance_details');
    }
}
