<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCadreStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cadre_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('cadre_id');
            $table->unsignedBigInteger('step_id');
            $table->unsignedBigInteger('monthly_basic');
            $table->timestamps();

            $table->foreign('cadre_id')->references('id')->on('cadres');
            $table->foreign('step_id')->references('id')->on('steps');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cadre_steps');
    }
}
