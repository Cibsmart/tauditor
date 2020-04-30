<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDeductionNamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deduction_names', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('deduction_type_id');
            $table->string('code', 20);
            $table->string('name');
            $table->string('domain_id');
            $table->timestamps();

            $table->foreign('domain_id')->references('id')->on('domains');
            $table->foreign('deduction_type_id')->references('id')->on('deduction_types');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('deduction_names');
    }
}
