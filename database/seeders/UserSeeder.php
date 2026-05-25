<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Programmer (PIC)
        User::create([
            'nik'      => '10001',
            'name'     => 'Budi Programmer',
            'email'    => 'programmer@demo.com',
            'phone_wa' => '6281234560001',   // contoh format internasional
            'password' => Hash::make('password'),
            'role'     => 'programmer',
        ]);

        // Project Manager
        User::create([
            'nik'      => '20001',
            'name'     => 'Sari Project Manager',
            'email'    => 'pm@demo.com',
            'phone_wa' => '6281234560002',
            'password' => Hash::make('password'),
            'role'     => 'project_manager',
        ]);
    }
}
