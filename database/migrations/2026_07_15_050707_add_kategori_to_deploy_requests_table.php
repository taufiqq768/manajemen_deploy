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
        Schema::table('deploy_requests', function (Blueprint $table) {
            $table->enum('kategori', ['cr', 'enhancement', 'bug_fixing'])->nullable()->after('id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('deploy_requests', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
