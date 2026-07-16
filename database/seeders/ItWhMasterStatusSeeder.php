<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ItWhMasterStatus;

class ItWhMasterStatusSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            // =====================================
            // PROJECT APP DEV & NON-APP
            // =====================================
            [
                'name' => 'Not Started',
                'category' => 'Project App',
                'weight' => 0,
                'color' => '#64748b', // slate-500
                'sort_order' => 1,
            ],
            [
                'name' => 'Development',
                'category' => 'Project App',
                'weight' => 50,
                'color' => '#3b82f6', // blue-500
                'sort_order' => 2,
            ],
            [
                'name' => 'Live',
                'category' => 'Project App',
                'weight' => 100,
                'color' => '#10b981', // emerald-500
                'sort_order' => 3,
            ],
            [
                'name' => 'Live w/ CR',
                'category' => 'Project App',
                'weight' => 100,
                'color' => '#8b5cf6', // violet-500
                'sort_order' => 4,
            ],
            [
                'name' => 'Live w/ Bug',
                'category' => 'Project App',
                'weight' => 100,
                'color' => '#f59e0b', // amber-500
                'sort_order' => 5,
            ],
            [
                'name' => 'Hold',
                'category' => 'Project App',
                'weight' => 0,
                'color' => '#f43f5e', // rose-500
                'sort_order' => 6,
            ],
            [
                'name' => 'Dropped',
                'category' => 'Project App',
                'weight' => 0,
                'color' => '#475569', // slate-600
                'sort_order' => 7,
            ],
            // Project Non-App (Same as App)
            [
                'name' => 'Not Started',
                'category' => 'Project Non-App',
                'weight' => 0,
                'color' => '#64748b', // slate-500
                'sort_order' => 1,
            ],
            [
                'name' => 'Development',
                'category' => 'Project Non-App',
                'weight' => 50,
                'color' => '#3b82f6', // blue-500
                'sort_order' => 2,
            ],
            [
                'name' => 'Live',
                'category' => 'Project Non-App',
                'weight' => 100,
                'color' => '#10b981', // emerald-500
                'sort_order' => 3,
            ],
            [
                'name' => 'Live w/ CR',
                'category' => 'Project Non-App',
                'weight' => 100,
                'color' => '#8b5cf6', // violet-500
                'sort_order' => 4,
            ],
            [
                'name' => 'Live w/ Bug',
                'category' => 'Project Non-App',
                'weight' => 100,
                'color' => '#f59e0b', // amber-500
                'sort_order' => 5,
            ],
            [
                'name' => 'Hold',
                'category' => 'Project Non-App',
                'weight' => 0,
                'color' => '#f43f5e', // rose-500
                'sort_order' => 6,
            ],
            [
                'name' => 'Dropped',
                'category' => 'Project Non-App',
                'weight' => 0,
                'color' => '#475569', // slate-600
                'sort_order' => 7,
            ],

            // =====================================
            // ACTIVITY APP DEV & NON-APP
            // =====================================
            [
                'name' => 'Not Started',
                'category' => 'Activity',
                'weight' => 0,
                'color' => '#64748b', // slate-500
                'sort_order' => 1,
            ],
            [
                'name' => 'Ureq Analysis',
                'category' => 'Activity',
                'weight' => 10,
                'color' => '#3b82f6', // blue-500
                'sort_order' => 2,
            ],
            [
                'name' => 'Programming',
                'category' => 'Activity',
                'weight' => 50,
                'color' => '#6366f1', // indigo-500
                'sort_order' => 3,
            ],
            [
                'name' => 'Tech Testing',
                'category' => 'Activity',
                'weight' => 70,
                'color' => '#8b5cf6', // violet-500
                'sort_order' => 4,
            ],
            [
                'name' => 'SIT',
                'category' => 'Activity',
                'weight' => 80,
                'color' => '#d946ef', // fuchsia-500
                'sort_order' => 5,
            ],
            [
                'name' => 'UAT',
                'category' => 'Activity',
                'weight' => 90,
                'color' => '#ec4899', // pink-500
                'sort_order' => 6,
            ],
            [
                'name' => 'Done',
                'category' => 'Activity',
                'weight' => 100,
                'color' => '#10b981', // emerald-500
                'sort_order' => 7,
            ],
            [
                'name' => 'Hold',
                'category' => 'Activity',
                'weight' => 0,
                'color' => '#f43f5e', // rose-500
                'sort_order' => 8,
            ],

            // =====================================
            // GOVERNANCE
            // =====================================
            [
                'name' => 'Not Started',
                'category' => 'Governance',
                'weight' => 0,
                'color' => '#64748b', // slate-500
                'sort_order' => 1,
            ],
            [
                'name' => 'On Progress',
                'category' => 'Governance',
                'weight' => 50, // This is often manual, defaulting to 50
                'color' => '#3b82f6', // blue-500
                'sort_order' => 2,
            ],
            [
                'name' => 'Done',
                'category' => 'Governance',
                'weight' => 100,
                'color' => '#10b981', // emerald-500
                'sort_order' => 3,
            ],
            [
                'name' => 'Hold',
                'category' => 'Governance',
                'weight' => 0,
                'color' => '#f43f5e', // rose-500
                'sort_order' => 4,
            ],
        ];

        foreach ($statuses as $status) {
            ItWhMasterStatus::firstOrCreate([
                'name' => $status['name'],
                'category' => $status['category'],
            ], $status);
        }
    }
}
