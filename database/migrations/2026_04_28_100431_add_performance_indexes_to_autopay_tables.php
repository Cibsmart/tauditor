<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('microfinance_bank_schedules', function (Blueprint $table) {
            $table->index('audit_sub_mda_schedule_id');
        });

        Schema::table('audit_mda_schedules', function (Blueprint $table) {
            $table->index('uploaded');
            $table->index('autopay_generated');
        });

        Schema::table('audit_sub_mda_schedules', function (Blueprint $table) {
            $table->index('uploaded');
            $table->index('autopay_generated');
        });
    }

    public function down(): void
    {
        Schema::table('microfinance_bank_schedules', function (Blueprint $table) {
            $table->dropIndex(['audit_sub_mda_schedule_id']);
        });

        Schema::table('audit_mda_schedules', function (Blueprint $table) {
            $table->dropIndex(['uploaded']);
            $table->dropIndex(['autopay_generated']);
        });

        Schema::table('audit_sub_mda_schedules', function (Blueprint $table) {
            $table->dropIndex(['uploaded']);
            $table->dropIndex(['autopay_generated']);
        });
    }
};
