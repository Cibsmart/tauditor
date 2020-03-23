<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructureGradeLevelStepsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_grade_level_steps', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('structure_grade_level_id');
            $table->unsignedBigInteger('step_id');
            $table->timestamps();

            $table->foreign('structure_grade_level_id')->references('id')->on('structure_grade_levels');
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
        Schema::dropIfExists('structure_grade_level_steps');
    }
}
