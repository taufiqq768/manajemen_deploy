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
        if (Schema::hasTable('deploy_requests')) {
            Schema::table('deploy_requests', function (Blueprint $table) {
                if (!Schema::hasColumn('deploy_requests', 'document_link')) {
                    $table->string('document_link', 500)->nullable()->after('document_support');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deploy_requests')) {
            Schema::table('deploy_requests', function (Blueprint $table) {
                if (Schema::hasColumn('deploy_requests', 'document_link')) {
                    $table->dropColumn('document_link');
                }
            });
        }
    }
};
