<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // ID dari API eksternal (gup.ptpn1.co.id) — dipakai sebagai key upsert
            $table->unsignedInteger('api_id')->nullable()->unique()->after('id');

            // URL live aplikasi dari API (berbeda dengan repo_url yang merupakan git repo)
            $table->string('app_url', 255)->nullable()->after('repo_url');

            // PIC dibuat nullable agar aplikasi baru hasil sync dari API bisa disimpan
            // sebelum admin sempat menetapkan PIC
            $table->foreignId('pic_user_id')->nullable()->change();

            // Catat kapan terakhir kali data di-sync dari API
            $table->timestamp('synced_at')->nullable()->after('app_url');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->dropColumn(['api_id', 'app_url', 'synced_at']);
            $table->foreignId('pic_user_id')->nullable(false)->change();
        });
    }
};
