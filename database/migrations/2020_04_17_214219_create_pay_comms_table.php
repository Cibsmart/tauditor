<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayCommsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pay_comms', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('name');
            $table->string('account_number');
            $table->unsignedBigInteger('commission');
            $table->string('bankable_type');
            $table->unsignedBigInteger('bankable_id');
            $table->string('domain_id');
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
        Schema::dropIfExists('pay_comms');
    }
}
