<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnToAuditPayrollCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_payroll_categories', function (Blueprint $table) {
            $table->string('analysis_status', 50)
                ->after('staff_type')->default('pending');
            $table->string('autopay_status', 50)
                ->after('staff_type')->default('pending');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_payroll_categories', function (Blueprint $table) {
            //
        });
    }
}
