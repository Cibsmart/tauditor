<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePaymentCredentialsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_credentials', function (Blueprint $table) {
            $table->id();
            $table->string('corporate_code');
            $table->string('payment_type_id');
            $table->string('terminal_id', 20);
            $table->string('account_number');
            $table->string('account_name');
            $table->string('pan');
            $table->enum('account_type', ['00', '10', '20']);
            $table->unsignedBigInteger('bank_id');
            $table->boolean('is_single_debit')->default(0);
            $table->string('beneficiary_type_id');
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
        Schema::dropIfExists('payment_credentials');
    }
}
