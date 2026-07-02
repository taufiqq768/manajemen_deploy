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
            if (!Schema::hasColumn('deploy_requests', 'url_token')) {
                Schema::table('deploy_requests', function (Blueprint $table) {
                    $table->string('url_token', 10)->nullable()->after('rejection_reason');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('deploy_requests')) {
            if (Schema::hasColumn('deploy_requests', 'url_token')) {
                Schema::table('deploy_requests', function (Blueprint $table) {
                    $table->dropColumn('url_token');
                });
            }
        }
    }
};
