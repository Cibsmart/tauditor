<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_type_id');
            $table->unsignedBigInteger('deduction_name_id');
            $table->morphs('valuable');
            $table->timestamps();

            $table->foreign('deduction_type_id')->references('id')->on('deduction_types');
            $table->foreign('deduction_name_id')->references('id')->on('deduction_names');

            $table->unique(['deduction_type_id', 'deduction_name_id'], 'unique_deductions');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deductions');
    }
}
