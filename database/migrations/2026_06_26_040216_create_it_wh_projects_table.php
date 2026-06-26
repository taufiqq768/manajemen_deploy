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
        Schema::create('it_wh_projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('priority')->default('Medium');
            $table->string('status')->default('Not Started');
            $table->string('bpo')->nullable();
            $table->integer('progress')->default(0);
            $table->string('brd_document')->nullable();
            $table->text('pain_point_uraian')->nullable();
            $table->text('pain_point_impact')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_wh_projects');
    }
};
