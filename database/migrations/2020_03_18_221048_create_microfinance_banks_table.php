<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMicroFinanceBanksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('micro_finance_banks', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('account_number');
            $table->unsignedBigInteger('bank_id');
            $table->string('domain_id');
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('bank_id')->references('id')->on('banks');
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
        Schema::dropIfExists('micro_finance_banks');
    }
}
