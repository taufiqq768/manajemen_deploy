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
        if (!Schema::hasTable('it_wh_todos')) {
            Schema::create('it_wh_todos', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
                $table->foreignId('assigner_id')->constrained('users')->onDelete('cascade');
                $table->date('date');
                $table->string('task_name');
                $table->date('deadline');
                $table->string('status')->default('To Do'); // 'To Do', 'In Progress', 'Done'
                $table->text('notes')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('it_wh_todos');
    }
};
