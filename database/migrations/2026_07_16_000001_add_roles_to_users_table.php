<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Alter ENUM safely via raw SQL to avoid needing doctrine/dbal package
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('programmer', 'project_manager', 'admin', 'governance', 'operational') NOT NULL DEFAULT 'programmer'");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE users MODIFY COLUMN role ENUM('programmer', 'project_manager', 'admin') NOT NULL DEFAULT 'programmer'");
    }
};
