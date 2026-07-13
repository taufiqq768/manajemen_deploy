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
        if (!Schema::hasTable('it_wh_project_group_project')) {
            Schema::create('it_wh_project_group_project', function (Blueprint $table) {
                $table->id();
                $table->foreignId('it_wh_project_group_id')->constrained()->onDelete('cascade');
                $table->foreignId('it_wh_project_id')->constrained()->onDelete('cascade');
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_wh_project_group_project');
    }
};
