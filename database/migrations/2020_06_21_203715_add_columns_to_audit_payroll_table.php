<?php

use Carbon\Carbon;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddColumnsToAuditPayrollTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('audit_payrolls', function (Blueprint $table) {
            $table->dateTime('timestamp')->after('domain_id')->default(Carbon::parse('25 June 2020'));
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_payrolls', function (Blueprint $table) {
            $table->dropColumn('timestamp');
        });
    }
}
