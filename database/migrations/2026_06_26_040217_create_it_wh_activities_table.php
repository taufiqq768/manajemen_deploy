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
        if (!Schema::hasTable('it_wh_activities')) {
            Schema::create('it_wh_activities', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_project_id')->constrained()->cascadeOnDelete();
                $table->string('type')->default('Fitur');
                $table->string('name');
                $table->date('start_date')->nullable();
                $table->date('deadline')->nullable();
                $table->date('adjustment_date')->nullable();
                $table->string('notes')->nullable();
                $table->string('document_link')->nullable();
                $table->string('status')->default('Not Started');
                $table->integer('sort_order')->default(0);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('it_wh_activities')) {
            Schema::dropIfExists('it_wh_activities');
        }
    }
};