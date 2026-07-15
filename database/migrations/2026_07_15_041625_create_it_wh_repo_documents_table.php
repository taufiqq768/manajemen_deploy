<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('it_wh_repo_documents', function (Blueprint $table) {
            $table->id();
            $table->foreignId('it_wh_repo_type_id')->constrained('it_wh_repo_types')->cascadeOnDelete();
            $table->foreignId('it_wh_repo_sub_type_id')->nullable()->constrained('it_wh_repo_sub_types')->nullOnDelete();
            $table->string('name');
            $table->string('version')->nullable(); // teks bebas
            $table->date('document_date')->nullable();
            $table->string('file_path')->nullable();
            $table->string('link', 2048)->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('it_wh_repo_documents');
    }
};
