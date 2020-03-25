<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadreStepAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadre_step_allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allowance_id');
            $table->unsignedBigInteger('cadre_step_id');
            $table->timestamps();

            $table->foreign('allowance_id')->references('id')->on('allowances');
            $table->foreign('cadre_step_id')->references('id')->on('cadre_steps');

            $table->unique(['allowance_id', 'cadre_step_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadre_step_allowances');
    }
}
