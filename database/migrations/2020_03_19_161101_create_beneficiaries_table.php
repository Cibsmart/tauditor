<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiariesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiaries', function (Blueprint $table) {
            $table->id();
            $table->string('verification_number')->nullable();
            $table->string('last_name');
            $table->string('first_name');
            $table->string('middle_name')->nullable();
            $table->date('date_of_birth');
            $table->unsignedBigInteger('gender_id')->nullable();
            $table->unsignedBigInteger('marital_status_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('local_government_id')->nullable();
            $table->string('phone_number', 50)->nullable();
            $table->string('email')->nullable();
            $table->string('address_line_1')->nullable();
            $table->string('address_line_2')->nullable();
            $table->string('address_city')->nullable();
            $table->string('address_state')->nullable();
            $table->string('address_country')->nullable();
            $table->string('domain_id');
            $table->unsignedBigInteger('beneficiary_type_id');
            $table->boolean('pensioner')->default(0);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('domain_id')->references('id')->on('domains');
            $table->foreign('beneficiary_type_id')->references('id')->on('beneficiary_types');
            $table->foreign('gender_id')->references('id')->on('genders');
            $table->foreign('marital_status_id')->references('id')->on('marital_statuses');
            $table->foreign('state_id')->references('id')->on('states');
            $table->foreign('local_government_id')->references('id')->on('local_governments');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiaries');
    }
}
