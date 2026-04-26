<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('schedule_zips', function (Blueprint $table) {
            $table->id();
            $table->foreignId('audit_payroll_category_id')
                ->constrained()
                ->cascadeOnDelete();
            $table->string('type', 16);
            $table->string('status', 16);
            $table->timestamp('built_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();

            $table->unique(['audit_payroll_category_id', 'type']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('schedule_zips');
    }
};
