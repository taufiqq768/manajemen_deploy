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
        if (!Schema::hasTable('deploy_requests')) {
            Schema::create('deploy_requests', function (Blueprint $table) {
                $table->id();
                $table->foreignId('application_id')->constrained('applications')->cascadeOnDelete();
                $table->foreignId('requester_id')->constrained('users')->cascadeOnDelete();
                $table->foreignId('approver_id')->nullable()->constrained('users')->nullOnDelete();
                $table->string('version', 50);
                $table->text('release_notes');
                $table->text('release_impact')->nullable();
                $table->text('document_support')->nullable();
                $table->enum('environment', ['production'])->default('production');
                $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
                $table->timestamp('scheduled_at')->nullable();
                $table->timestamp('approved_at')->nullable();
                $table->text('rejection_reason')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deploy_requests')) {
            Schema::dropIfExists('deploy_requests');
        }
    }
};