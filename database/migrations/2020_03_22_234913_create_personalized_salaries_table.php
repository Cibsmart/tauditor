<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePersonalizedSalariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('personalized_salaries', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('salary_detail_id');
            $table->timestamps();

            $table->foreign('salary_detail_id')->references('id')->on('salary_details');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('personalized_salaries');
    }
}
