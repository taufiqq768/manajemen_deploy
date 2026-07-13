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
        if (!Schema::hasTable('it_wh_nonapp_projects')) {
            Schema::create('it_wh_nonapp_projects', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('priority')->default('Medium');
                $table->string('status')->default('Not Started');
                $table->string('bpo')->nullable();
                $table->integer('progress')->default(0);
                $table->text('pain_point_uraian')->nullable();
                $table->text('pain_point_impact')->nullable();
                $table->date('start_date')->nullable();
                $table->date('deadline')->nullable();
                $table->date('adjustment_date')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_nonapp_project_user')) {
            Schema::create('it_wh_nonapp_project_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_nonapp_project_id')->constrained('it_wh_nonapp_projects')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_nonapp_activities')) {
            Schema::create('it_wh_nonapp_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_nonapp_project_id')->constrained('it_wh_nonapp_projects')->cascadeOnDelete();
                $table->string('name');
                $table->date('start_date')->nullable();
                $table->date('deadline')->nullable();
                $table->date('adjustment_date')->nullable();
                $table->text('notes')->nullable();
                $table->string('status')->default('Not Started');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_nonapp_activity_user')) {
            Schema::create('it_wh_nonapp_activity_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_nonapp_activity_id')->constrained('it_wh_nonapp_activities')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_nonapp_documents')) {
            Schema::create('it_wh_nonapp_documents', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_nonapp_project_id')->constrained('it_wh_nonapp_projects')->cascadeOnDelete();
                $table->string('description');
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
        Schema::dropIfExists('it_wh_nonapp_documents');
        Schema::dropIfExists('it_wh_nonapp_activity_user');
        Schema::dropIfExists('it_wh_nonapp_activities');
        Schema::dropIfExists('it_wh_nonapp_project_user');
        Schema::dropIfExists('it_wh_nonapp_projects');
    }
};
