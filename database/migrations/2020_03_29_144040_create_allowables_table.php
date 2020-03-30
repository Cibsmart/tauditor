<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAllowablesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('allowables', function (Blueprint $table) {
            $table->id();
            $table->morphs('allowable');
            $table->unsignedBigInteger('allowance_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('allowance_id')->references('id')->on('allowances');

            $table->unique(['allowable_type', 'allowable_id', 'allowance_id'], 'unique_allowable_allowance');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('allowables');
    }
}
