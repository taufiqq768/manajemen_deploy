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
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'version_api_get')) {
                    $table->string('version_api_get', 255)->nullable()->after('version');
                }
                if (!Schema::hasColumn('applications', 'version_api_write')) {
                    $table->string('version_api_write', 255)->nullable()->after('version_api_get');
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
                $toDrop = [];
                if (Schema::hasColumn('applications', 'version_api_get')) $toDrop[] = 'version_api_get';
                if (Schema::hasColumn('applications', 'version_api_write')) $toDrop[] = 'version_api_write';
                if (!empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
            });
        }
    }
};
