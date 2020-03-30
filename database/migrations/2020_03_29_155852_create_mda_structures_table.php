<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMdaStructuresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('mda_structures', function (Blueprint $table) {
            $table->id();
            $table->timestamps();

            $table->foreignId('mda_id');
            $table->foreignId('structure_id');

            $table->unique(['mda_id', 'structure_id'], 'unique_mda_structure');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('mda_structures');
    }
}
