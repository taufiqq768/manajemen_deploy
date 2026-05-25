<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            // Hapus foreign key constraint terlebih dahulu, lalu drop kolom
            $table->dropForeign(['pic_user_id']);
            $table->dropColumn('pic_user_id');
        });
    }

    public function down(): void
    {
        Schema::table('applications', function (Blueprint $table) {
            $table->foreignId('pic_user_id')->nullable()->constrained('users')->cascadeOnDelete();
        });
    }
};
