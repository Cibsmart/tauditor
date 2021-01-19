<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateStaffInfosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('staff_infos', function (Blueprint $table) {
            $table->id();
            $table->string('staff_id');
            $table->string('staff_type')->nullable();
            $table->string('grade')->nullable();
            $table->string('step')->nullable();
            $table->string('ministry')->nullable();
            $table->string('department')->nullable();
            $table->string('image')->nullable();
            $table->string('finger_print_1')->nullable();
            $table->string('finger_print_2')->nullable();
            $table->string('finger_print_3')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('staff_infos');
    }
}
