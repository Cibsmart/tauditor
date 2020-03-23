<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructureGradeLevelsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structure_grade_levels', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('structure_id');
            $table->unsignedBigInteger('grade_level_id');
            $table->timestamps();

            $table->foreign('structure_id')->references('id')->on('structures');
            $table->foreign('grade_level_id')->references('id')->on('grade_levels');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('structure_grade_levels');
    }
}
