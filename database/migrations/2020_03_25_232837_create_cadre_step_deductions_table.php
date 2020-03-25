<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadreStepDeductionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadre_step_deductions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_id');
            $table->unsignedBigInteger('cadre_step_id');
            $table->timestamps();

            $table->foreign('deduction_id')->references('id')->on('deductions');
            $table->foreign('cadre_step_id')->references('id')->on('cadre_steps');

            $table->unique(['deduction_id', 'cadre_step_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadre_step_deductions');
    }
}
