<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAuditSubMdaSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_reports', function (Blueprint $table) {
            $table->unsignedBigInteger('audit_sub_mda_schedule_id')
                ->after('audit_payroll_category_id')
                ->default(1);
            $table->softDeletes();

            $table->foreign('audit_sub_mda_schedule_id')->references('id')->on('audit_sub_mda_schedules');
        });

        Schema::table('autopay_schedules', function (Blueprint $table) {
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
        Schema::table('audit_sub_mda_schedules', function (Blueprint $table) {
            //
        });
    }
}
