<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayeDataUploadsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('paye_data_uploads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained();
            $table->foreignId('audit_payroll_category_id')->constrained();
            $table->string('status');
            $table->json('response');
            $table->boolean('successful')->default(0);
            $table->boolean('failed')->default(0);
            $table->boolean('client')->default(0);
            $table->boolean('server')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('paye_data_uploads');
    }
}
