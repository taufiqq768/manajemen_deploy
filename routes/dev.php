<?php

use Illuminate\Support\Facades\Route;

Route::get('/autologin/{role?}', function ($role = 'programmer') {
    // Map Indonesian role spelling to matching DB enum value
    if ($role === 'project_manajer') {
        $role = 'project_manager';
    }

    $user = \App\Models\User::firstOrCreate(
        ['email' => $role . '@example.com'],
        [
            'name' => 'Dummy ' . ucfirst(str_replace('_', ' ', $role)),
            'password' => bcrypt('password'),
            'role' => $role
        ]
    );

    // Hubungkan programmer baru ke semua aplikasi agar form Pengajuan Deploy langsung terisi untuk testing
    if ($user->isProgrammer()) {
        $applications = \App\Models\Application::all();
        foreach ($applications as $app) {
            if (!$app->pics()->where('users.id', $user->id)->exists()) {
                $app->pics()->attach($user->id);
            }
        }
    }

    auth()->login($user);
    return redirect('/dashboard');
});
