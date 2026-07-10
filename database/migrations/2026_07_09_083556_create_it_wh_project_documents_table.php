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
        if (!Schema::hasTable('it_wh_project_documents')) {
            Schema::create('it_wh_project_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_project_id')->constrained('it_wh_projects')->onDelete('cascade');
                $table->enum('type', ['PIR', 'Dokumen']);
                $table->text('description')->nullable();
                $table->date('document_date')->nullable();
                $table->string('file_path')->nullable();
                $table->string('link')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_wh_project_documents');
    }
};
