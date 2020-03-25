<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowances', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('allowance_type_id');
            $table->unsignedBigInteger('allowance_name_id');
            $table->morphs('valuable');
            $table->timestamps();

            $table->foreign('allowance_type_id')->references('id')->on('allowance_types');
            $table->foreign('allowance_name_id')->references('id')->on('allowance_names');

            $table->unique(['allowance_type_id', 'allowance_name_id'], 'unique_allowances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowances');
    }
}
