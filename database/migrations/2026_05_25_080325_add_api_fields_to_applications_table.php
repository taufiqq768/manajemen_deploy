<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                if (!Schema::hasColumn('applications', 'api_id')) {
                    // ID dari API eksternal (gup.ptpn1.co.id) — dipakai sebagai key upsert
                    $table->unsignedInteger('api_id')->nullable()->unique()->after('id');
                }

                if (!Schema::hasColumn('applications', 'app_url')) {
                    // URL live aplikasi dari API (berbeda dengan repo_url yang merupakan git repo)
                    $table->string('app_url', 255)->nullable()->after('repo_url');
                }

                if (Schema::hasColumn('applications', 'pic_user_id')) {
                    // PIC dibuat nullable agar aplikasi baru hasil sync dari API bisa disimpan
                    // sebelum admin sempat menetapkan PIC
                    $table->foreignId('pic_user_id')->nullable()->change();
                }

                if (!Schema::hasColumn('applications', 'synced_at')) {
                    // Catat kapan terakhir kali data di-sync dari API
                    $table->timestamp('synced_at')->nullable()->after('app_url');
                }
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('applications')) {
            Schema::table('applications', function (Blueprint $table) {
                $toDrop = [];
                if (Schema::hasColumn('applications', 'api_id')) $toDrop[] = 'api_id';
                if (Schema::hasColumn('applications', 'app_url')) $toDrop[] = 'app_url';
                if (Schema::hasColumn('applications', 'synced_at')) $toDrop[] = 'synced_at';
                if (!empty($toDrop)) {
                    $table->dropColumn($toDrop);
                }
                if (Schema::hasColumn('applications', 'pic_user_id')) {
                    $table->foreignId('pic_user_id')->nullable(false)->change();
                }
            });
        }
    }
};