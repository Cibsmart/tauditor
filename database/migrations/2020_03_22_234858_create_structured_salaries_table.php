<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStructuredSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('structured_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_detail_id');
            $table->unsignedBigInteger('salary_structure_id');
            $table->unsignedBigInteger('grade_level_id');
            $table->unsignedBigInteger('step_id');
            $table->timestamps();

            $table->foreign('salary_detail_id')->references('id')->on('salary_details');
            $table->foreign('salary_structure_id')->references('id')->on('salary_structures');
            $table->foreign('grade_level_id')->references('id')->on('grade_levels');
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
        Schema::dropIfExists('structured_salaries');
    }
}
