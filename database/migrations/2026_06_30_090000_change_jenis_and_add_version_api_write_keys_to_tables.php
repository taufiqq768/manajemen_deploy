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
                // Change jenis from enum to string to support multiple select values (saved as JSON array)
                $table->string('jenis', 255)->change()->nullable();
            });
        }

        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'version_api_write_key')) {
                    $table->string('version_api_write_key', 100)->nullable()->after('version_api_write')->default('version');
                }
                if (!Schema::hasColumn('applications', 'version_api_write_notes_key')) {
                    $table->string('version_api_write_notes_key', 100)->nullable()->after('version_api_write_key')->default('release_notes');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (Schema::hasColumn('applications', 'version_api_write_key')) {
                    $table->dropColumn('version_api_write_key');
                }
                if (Schema::hasColumn('applications', 'version_api_write_notes_key')) {
                    $table->dropColumn('version_api_write_notes_key');
                }
            });
        }

        if (Schema::hasTable('deploy_requests')) {
            Schema::table('deploy_requests', function (Blueprint $table) {
                // We don't change back to enum because it might contain incompatible data
                $table->string('jenis', 50)->change();
            });
        }
    }
};
