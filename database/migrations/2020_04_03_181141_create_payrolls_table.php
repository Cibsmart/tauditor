<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('month');
            $table->string('month_name', 20);
            $table->unsignedBigInteger('year');
            $table->boolean('approved')->default(0);
            $table->boolean('archived')->default(0);
            $table->timestamp('generated')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->unsignedBigInteger('domain_id');
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['month', 'month_name', 'year', 'domain_id'], 'unique_payrolls');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('payrolls');
    }
}
