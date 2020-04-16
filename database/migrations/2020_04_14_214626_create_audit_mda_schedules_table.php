<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditMdaSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_mda_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_payroll_id');
            $table->unsignedBigInteger('mda_id');
            $table->string('mda_name');
            $table->unsignedBigInteger('total_net_pay')->nullable();
            $table->unsignedInteger('head_count')->nullable();
            $table->boolean('uploaded')->default(0);
            $table->boolean('pension')->default(0);
            $table->boolean('has_sub')->default(0);
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
        Schema::dropIfExists('audit_mda_schedules');
    }
}
