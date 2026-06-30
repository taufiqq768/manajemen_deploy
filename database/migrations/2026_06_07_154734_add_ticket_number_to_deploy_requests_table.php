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
                if (!Schema::hasColumn('deploy_requests', 'ticket_number')) {
                    $table->string('ticket_number')->nullable()->unique()->after('id');
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
                if (Schema::hasColumn('deploy_requests', 'ticket_number')) {
                    $table->dropColumn('ticket_number');
                }
            });
        }
    }
};