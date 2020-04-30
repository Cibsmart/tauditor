<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_types', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20);
            $table->string('name');
            $table->string('domain_id');
            $table->timestamps();

            $table->foreign('domain_id')->references('id')->on('domains');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deduction_types');
    }
}
