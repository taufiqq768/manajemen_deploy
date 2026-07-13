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
        if (!Schema::hasTable('it_wh_governances')) {
            Schema::create('it_wh_governances', function (Blueprint $table) {
                $table->id();
                $table->string('name');
                $table->text('description')->nullable();
                $table->string('priority')->default('Medium');
                $table->string('status')->default('Not Started');
                $table->integer('progress')->default(0);
                $table->text('progress_notes')->nullable();
                $table->date('progress_date')->nullable();
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_governance_user')) {
            Schema::create('it_wh_governance_user', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_governance_id')->constrained('it_wh_governances')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_governance_activities')) {
            Schema::create('it_wh_governance_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_governance_id')->constrained('it_wh_governances')->cascadeOnDelete();
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

        if (!Schema::hasTable('it_wh_governance_activity_user')) {
            Schema::create('it_wh_governance_activity_user', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('it_wh_governance_activity_id');
                $table->foreign('it_wh_governance_activity_id', 'fk_gov_act_user_act_id')
                      ->references('id')->on('it_wh_governance_activities')->cascadeOnDelete();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->timestamps();
            });
        }

        if (!Schema::hasTable('it_wh_governance_documents')) {
            Schema::create('it_wh_governance_documents', function (Blueprint $table) {
                $table->id();
                $table->unsignedBigInteger('it_wh_governance_id');
                $table->foreign('it_wh_governance_id', 'fk_gov_doc_gov_id')
                      ->references('id')->on('it_wh_governances')->cascadeOnDelete();
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
        Schema::dropIfExists('it_wh_governance_documents');
        Schema::dropIfExists('it_wh_governance_activity_user');
        Schema::dropIfExists('it_wh_governance_activities');
        Schema::dropIfExists('it_wh_governance_user');
        Schema::dropIfExists('it_wh_governances');
    }
};
