<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSalaryStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('salary_structures', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('structure_id');
            $table->unsignedBigInteger('grade_level_id');
            $table->unsignedBigInteger('step_id');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['structure_id', 'grade_level_id', 'step_id', 'deleted_at'], 'unique_salary_structure_items');
            $table->foreign('structure_id')->references('id')->on('structures');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('salary_structures');
    }
}
