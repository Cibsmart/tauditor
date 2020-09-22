<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePotentialUserMfbsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('potential_user_mfbs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('potential_user_id')
                  ->constrained()
                  ->onDelete('cascade');
            $table->foreignId('micro_finance_bank_id')->constrained();
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
        Schema::dropIfExists('potential_user_mfbs');
    }
}
