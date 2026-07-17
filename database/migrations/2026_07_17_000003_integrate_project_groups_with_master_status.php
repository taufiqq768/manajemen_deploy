<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('it_wh_project_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('name');
        });

        // Map existing status string to master status id
        $groups = DB::table('it_wh_project_groups')->get();
        foreach ($groups as $group) {
            $mappedName = match ($group->status) {
                'Progress' => 'Development',
                'Live (Bug Fixing)' => 'Live w/ Bug',
                'Retired' => 'Hold',
                default => $group->status,
            };

            $status = DB::table('it_wh_master_statuses')
                ->where('category', 'Project App')
                ->where('name', $mappedName)
                ->first();

            if (!$status) {
                $status = DB::table('it_wh_master_statuses')
                    ->where('category', 'Project App')
                    ->where('name', 'Not Started')
                    ->first();
            }

            DB::table('it_wh_project_groups')
                ->where('id', $group->id)
                ->update(['status_id' => $status->id]);
        }

        // Set status_id to non-nullable and add foreign key
        Schema::table('it_wh_project_groups', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable(false)->change();
            $table->foreign('status_id')->references('id')->on('it_wh_master_statuses')->onDelete('restrict');
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('it_wh_project_groups', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->string('status')->default('Not Started')->after('status_id');
        });

        // Reverse map
        $groups = DB::table('it_wh_project_groups')->get();
        foreach ($groups as $group) {
            $statusName = DB::table('it_wh_master_statuses')
                ->where('id', $group->status_id)
                ->value('name') ?: 'Not Started';

            DB::table('it_wh_project_groups')
                ->where('id', $group->id)
                ->update(['status' => $statusName]);
        }

        Schema::table('it_wh_project_groups', function (Blueprint $table) {
            $table->dropColumn('status_id');
        });
    }
};
