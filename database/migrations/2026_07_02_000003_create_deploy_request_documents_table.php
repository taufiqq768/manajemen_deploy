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
        if (!Schema::hasTable('deploy_request_documents')) {
            Schema::create('deploy_request_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('deploy_request_id')->constrained('deploy_requests')->cascadeOnDelete();
                $table->string('document_number', 150)->nullable();
                $table->string('file_path', 255)->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('deploy_request_documents');
    }
};
