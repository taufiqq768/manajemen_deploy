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
        if (!Schema::hasTable('version_logs')) {
            Schema::create('version_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
                $table->string('type', 20); // 'sync' (API Get) or 'write' (API Write)
                $table->string('old_version', 50)->nullable();
                $table->string('new_version', 50)->nullable();
                $table->string('status', 20); // 'success' or 'failed'
                $table->text('message')->nullable();
                $table->timestamp('created_at')->useCurrent();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('version_logs');
    }
};
