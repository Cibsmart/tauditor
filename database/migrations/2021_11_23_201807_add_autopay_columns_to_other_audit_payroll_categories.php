<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAutopayColumnsToOtherAuditPayrollCategories extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('other_audit_payroll_categories', function (Blueprint $table) {
            $table->dateTime('autopay_uploaded')->after('uploaded')->nullable();
            $table->dateTime('autopay_generated')->after('uploaded')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('other_audit_payroll_categories', function (Blueprint $table) {
            $table->dropColumn('autopay_uploaded', 'autopay_generated');
        });
    }
}
