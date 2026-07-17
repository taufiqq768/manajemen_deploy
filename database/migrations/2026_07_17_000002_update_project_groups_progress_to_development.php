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
        DB::table('it_wh_project_groups')
            ->where('status', 'Progress')
            ->update(['status' => 'Development']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('it_wh_project_groups')
            ->where('status', 'Development')
            ->update(['status' => 'Progress']);
    }
};
