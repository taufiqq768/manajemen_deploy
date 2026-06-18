<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('waha_connection_logs', function (Blueprint $table) {
            $table->id();
            $table->string('status')->comment('WORKING, STOPPED, STARTING, error, dll');
            $table->integer('response_time_ms')->nullable();
            $table->text('error_message')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('waha_connection_logs');
    }
};
