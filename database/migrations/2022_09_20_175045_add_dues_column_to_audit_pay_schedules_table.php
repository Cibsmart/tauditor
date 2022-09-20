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
        Schema::table('audit_pay_schedules', function (Blueprint $table) {
            $table->renameColumn('total_deduction', 'total_dues_deductions');

            $table->after('designation', function ($table) {
                $table->string('mda');
                $table->string('department');
            });

            $table->after('total_allowance', function ($table) {
                $table->unsignedBigInteger('total_dues')->default(0);
                $table->unsignedBigInteger('total_deductions')->default(0);
            });

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
