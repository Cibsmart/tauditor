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
            $table->boolean('analysed')->default(0);
            $table->boolean('autopay_generated')->default(0);
            $table->unsignedBigInteger('user_id');
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
        Schema::dropIfExists('audit_payrolls');
    }
}
