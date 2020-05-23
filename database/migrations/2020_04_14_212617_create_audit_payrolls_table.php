<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditPayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_payrolls', function (Blueprint $table) {
            $table->id();
            $table->unsignedSmallInteger('month');
            $table->string('month_name', 20);
            $table->unsignedBigInteger('year');
            $table->unsignedBigInteger('total_net_pay')->nullable();
            $table->unsignedBigInteger('head_count')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->string('domain_id');
            $table->timestamps();
            $table->softDeletes();

            $table->index(['year', 'month']);

            $table->foreign('user_id')->references('id')->on('users');
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
        Schema::dropIfExists('audit_payrolls');
    }
}
