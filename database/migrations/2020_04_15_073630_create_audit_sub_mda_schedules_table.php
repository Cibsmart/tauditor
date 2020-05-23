<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAuditSubMdaSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('audit_sub_mda_schedules', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('audit_mda_schedule_id');
            $table->string('sub_mda_name')->index();
            $table->boolean('uploaded')->default(0);
            $table->dateTime('analysed')->nullable();
            $table->dateTime('autopay_generated')->nullable();
            $table->dateTime('autopay_uploaded')->nullable();
            $table->unsignedBigInteger('total_net_pay')->nullable();
            $table->unsignedInteger('head_count')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('file_path')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('user_id')->references('id')->on('users');
            $table->foreign('audit_mda_schedule_id')->references('id')->on('audit_mda_schedules');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('audit_sub_mda_schedules');
    }
}
