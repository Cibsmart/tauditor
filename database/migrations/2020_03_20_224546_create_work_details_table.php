<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWorkDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('work_details', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('beneficiary_id');
            $table->unsignedBigInteger('designation_id');
            $table->unsignedBigInteger('grade_level_id');
            $table->unsignedBigInteger('step_id');
            $table->dateTime('date_of_appointment')->nullable();
            $table->string('place_of_appointment')->nullable();
            $table->dateTime('confirmed')->nullable();
            $table->dateTime('last_promotion_date')->nullable();
            $table->dateTime('retirement_date')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('work_details');
    }
}
