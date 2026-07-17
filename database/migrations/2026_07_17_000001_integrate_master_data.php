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
        // 1. Tambah kolom-kolom baru
        Schema::table('it_wh_projects', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('priority');
            $table->unsignedBigInteger('bpo_division_id')->nullable()->after('status_id');
            
            $table->foreign('status_id')->references('id')->on('it_wh_master_statuses')->nullOnDelete();
            $table->foreign('bpo_division_id')->references('id')->on('it_wh_master_divisions')->nullOnDelete();
        });

        Schema::table('it_wh_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('document_link');
            $table->foreign('status_id')->references('id')->on('it_wh_master_statuses')->nullOnDelete();
        });

        Schema::table('it_wh_nonapp_projects', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('priority');
            $table->unsignedBigInteger('bpo_division_id')->nullable()->after('status_id');
            
            $table->foreign('status_id')->references('id')->on('it_wh_master_statuses')->nullOnDelete();
            $table->foreign('bpo_division_id')->references('id')->on('it_wh_master_divisions')->nullOnDelete();
        });

        Schema::table('it_wh_nonapp_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('notes');
            $table->foreign('status_id')->references('id')->on('it_wh_master_statuses')->nullOnDelete();
        });

        Schema::table('it_wh_governance_activities', function (Blueprint $table) {
            $table->unsignedBigInteger('status_id')->nullable()->after('notes');
            $table->foreign('status_id')->references('id')->on('it_wh_master_statuses')->nullOnDelete();
        });

        // 2. Migrasi Data BPO (Divisi)
        // Ambil semua BPO unik dari kedua tabel project
        $bpos = DB::table('it_wh_projects')->whereNotNull('bpo')->where('bpo', '!=', '')->pluck('bpo')
            ->concat(DB::table('it_wh_nonapp_projects')->whereNotNull('bpo')->where('bpo', '!=', '')->pluck('bpo'))
            ->unique()
            ->map(fn($item) => trim($item))
            ->filter();

        $divisionMap = [];
        foreach ($bpos as $bpoName) {
            // Check if division exists in master
            $existing = DB::table('it_wh_master_divisions')->where('name', $bpoName)->first();
            if ($existing) {
                $divisionMap[$bpoName] = $existing->id;
            } else {
                $id = DB::table('it_wh_master_divisions')->insertGetId([
                    'name' => $bpoName,
                    'is_active' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
                $divisionMap[$bpoName] = $id;
            }
        }

        // 3. Ambil semua status master untuk pencocokan data
        $statusMap = DB::table('it_wh_master_statuses')->get()->groupBy('category');

        // Helper function untuk mendapatkan status_id
        $getStatusId = function($category, $statusName) use ($statusMap) {
            $statusName = trim($statusName);
            if (!isset($statusMap[$category])) {
                return null;
            }
            
            // Cari kecocokan exact
            $matched = $statusMap[$category]->first(function($s) use ($statusName) {
                return strcasecmp($s->name, $statusName) === 0;
            });

            if ($matched) {
                return $matched->id;
            }

            // Fallback: cari Not Started di kategori tersebut
            $fallback = $statusMap[$category]->first(function($s) {
                return strcasecmp($s->name, 'Not Started') === 0;
            });

            return $fallback ? $fallback->id : null;
        };

        // 4. Update data transaksi lama
        
        // it_wh_projects
        $oldProjects = DB::table('it_wh_projects')->get();
        foreach ($oldProjects as $p) {
            $statusId = $getStatusId('Project App', $p->status);
            $divisionId = isset($divisionMap[trim($p->bpo)]) ? $divisionMap[trim($p->bpo)] : null;
            
            DB::table('it_wh_projects')->where('id', $p->id)->update([
                'status_id' => $statusId,
                'bpo_division_id' => $divisionId
            ]);
        }

        // it_wh_activities
        $oldActivities = DB::table('it_wh_activities')->get();
        foreach ($oldActivities as $a) {
            $statusId = $getStatusId('Activity', $a->status);
            DB::table('it_wh_activities')->where('id', $a->id)->update([
                'status_id' => $statusId
            ]);
        }

        // it_wh_nonapp_projects
        $oldNonappProjects = DB::table('it_wh_nonapp_projects')->get();
        foreach ($oldNonappProjects as $p) {
            $statusId = $getStatusId('Project Non-App', $p->status);
            $divisionId = isset($divisionMap[trim($p->bpo)]) ? $divisionMap[trim($p->bpo)] : null;
            
            DB::table('it_wh_nonapp_projects')->where('id', $p->id)->update([
                'status_id' => $statusId,
                'bpo_division_id' => $divisionId
            ]);
        }

        // it_wh_nonapp_activities
        $oldNonappActivities = DB::table('it_wh_nonapp_activities')->get();
        foreach ($oldNonappActivities as $a) {
            $statusId = $getStatusId('Activity', $a->status);
            DB::table('it_wh_nonapp_activities')->where('id', $a->id)->update([
                'status_id' => $statusId
            ]);
        }

        // it_wh_governance_activities
        $oldGovActivities = DB::table('it_wh_governance_activities')->get();
        foreach ($oldGovActivities as $a) {
            $statusId = $getStatusId('Governance', $a->status);
            DB::table('it_wh_governance_activities')->where('id', $a->id)->update([
                'status_id' => $statusId
            ]);
        }

        // 5. Hapus kolom status lama
        Schema::table('it_wh_projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'bpo']);
        });

        Schema::table('it_wh_activities', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('it_wh_nonapp_projects', function (Blueprint $table) {
            $table->dropColumn(['status', 'bpo']);
        });

        Schema::table('it_wh_nonapp_activities', function (Blueprint $table) {
            $table->dropColumn('status');
        });

        Schema::table('it_wh_governance_activities', function (Blueprint $table) {
            $table->dropColumn('status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Reverse process: restore status and bpo columns and restore data
        Schema::table('it_wh_projects', function (Blueprint $table) {
            $table->string('status')->default('Not Started')->after('priority');
            $table->string('bpo')->nullable()->after('status');
        });

        Schema::table('it_wh_activities', function (Blueprint $table) {
            $table->string('status')->default('Not Started')->after('document_link');
        });

        Schema::table('it_wh_nonapp_projects', function (Blueprint $table) {
            $table->string('status')->default('Not Started')->after('priority');
            $table->string('bpo')->nullable()->after('status');
        });

        Schema::table('it_wh_nonapp_activities', function (Blueprint $table) {
            $table->string('status')->default('Not Started')->after('notes');
        });

        Schema::table('it_wh_governance_activities', function (Blueprint $table) {
            $table->string('status')->default('Not Started')->after('notes');
        });

        // Restore data
        // it_wh_projects
        $projects = DB::table('it_wh_projects')->get();
        foreach ($projects as $p) {
            $statusName = DB::table('it_wh_master_statuses')->where('id', $p->status_id)->value('name') ?: 'Not Started';
            $bpoName = DB::table('it_wh_master_divisions')->where('id', $p->bpo_division_id)->value('name');
            DB::table('it_wh_projects')->where('id', $p->id)->update([
                'status' => $statusName,
                'bpo' => $bpoName
            ]);
        }

        // it_wh_activities
        $activities = DB::table('it_wh_activities')->get();
        foreach ($activities as $a) {
            $statusName = DB::table('it_wh_master_statuses')->where('id', $a->status_id)->value('name') ?: 'Not Started';
            DB::table('it_wh_activities')->where('id', $a->id)->update([
                'status' => $statusName
            ]);
        }

        // it_wh_nonapp_projects
        $nonappProjects = DB::table('it_wh_nonapp_projects')->get();
        foreach ($nonappProjects as $p) {
            $statusName = DB::table('it_wh_master_statuses')->where('id', $p->status_id)->value('name') ?: 'Not Started';
            $bpoName = DB::table('it_wh_master_divisions')->where('id', $p->bpo_division_id)->value('name');
            DB::table('it_wh_nonapp_projects')->where('id', $p->id)->update([
                'status' => $statusName,
                'bpo' => $bpoName
            ]);
        }

        // it_wh_nonapp_activities
        $nonappActivities = DB::table('it_wh_nonapp_activities')->get();
        foreach ($nonappActivities as $a) {
            $statusName = DB::table('it_wh_master_statuses')->where('id', $a->status_id)->value('name') ?: 'Not Started';
            DB::table('it_wh_nonapp_activities')->where('id', $a->id)->update([
                'status' => $statusName
            ]);
        }

        // it_wh_governance_activities
        $govActivities = DB::table('it_wh_governance_activities')->get();
        foreach ($govActivities as $a) {
            $statusName = DB::table('it_wh_master_statuses')->where('id', $a->status_id)->value('name') ?: 'Not Started';
            DB::table('it_wh_governance_activities')->where('id', $a->id)->update([
                'status' => $statusName
            ]);
        }

        // Drop foreign keys and columns
        Schema::table('it_wh_projects', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['bpo_division_id']);
            $table->dropColumn(['status_id', 'bpo_division_id']);
        });

        Schema::table('it_wh_activities', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        Schema::table('it_wh_nonapp_projects', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropForeign(['bpo_division_id']);
            $table->dropColumn(['status_id', 'bpo_division_id']);
        });

        Schema::table('it_wh_nonapp_activities', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });

        Schema::table('it_wh_governance_activities', function (Blueprint $table) {
            $table->dropForeign(['status_id']);
            $table->dropColumn('status_id');
        });
    }
};
