<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBeneficiaryTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('beneficiary_types', function (Blueprint $table) {
            $table->string('id', 20)->unique();
            $table->string('name');
            $table->string('domain_id');
            $table->boolean('pensioners')->default(0);
            $table->timestamps();

            $table->unique(['name', 'domain_id'], 'unique_beneficiary_type');

            $table->foreign('domain_id')
                ->references('id')
                ->on('domains')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('beneficiary_types');
    }
}
