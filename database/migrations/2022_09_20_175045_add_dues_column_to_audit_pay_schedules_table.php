<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddDuesColumnToAuditPaySchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        // Rename must be in its own Schema::table() call so that Doctrine DBAL
        // (used for SQLite column renames) generates SQL against the pre-add
        // schema, rather than after the new columns have been appended.
        Schema::table('audit_pay_schedules', function (Blueprint $table) {
            $table->renameColumn('total_deduction', 'total_dues_deductions');
        });

        Schema::table('audit_pay_schedules', function (Blueprint $table) {
            $table->string('mda')->after('designation');
            $table->string('department')->after('mda');

            $table->unsignedBigInteger('total_dues')->default(0)->after('total_allowance');
            $table->unsignedBigInteger('total_deductions')->default(0)->after('total_dues');

            $table->json('dues')->after('allowances');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('audit_pay_schedules', function (Blueprint $table) {

            $table->dropColumn(['department', 'mda', 'total_dues', 'total_deductions', 'dues']);

            $table->renameColumn('total_dues_deductions', 'total_deduction');
        });
    }
}
