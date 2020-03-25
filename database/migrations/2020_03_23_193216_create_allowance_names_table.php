<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowanceNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowance_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allowance_type_id');
            $table->string('code', 20);
            $table->string('name');
            $table->timestamps();

            $table->foreign('allowance_type_id')->references('id')->on('allowance_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowance_names');
    }
}
