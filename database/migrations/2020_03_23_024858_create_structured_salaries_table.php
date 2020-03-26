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
            $table->unsignedBigInteger('cadre_step_id');
            $table->timestamps();

            $table->foreign('cadre_step_id')
                  ->references('id')
                  ->on('cadre_steps');
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
