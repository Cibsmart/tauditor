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
            $table->unsignedBigInteger('cadre_step_id');
            $table->timestamps();

            $table->foreign('salary_detail_id')->references('id')->on('salary_details');
            $table->foreign('cadre_step_id')
                  ->references('id')
                  ->on('cadre_steps');

            $table->unique(['salary_detail_id', 'cadre_step_id'], 'unique_structured_salaries');
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
