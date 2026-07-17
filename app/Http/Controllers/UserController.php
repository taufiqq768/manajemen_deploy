<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index()
    {
        // Hanya nampilin semua user
        $users = User::latest()->paginate(10);
        return view('users.index', compact('users'));
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|string|email|max:255|unique:users',
            'nik'      => 'required|string|max:50|unique:users',
            'phone_wa' => 'nullable|string|max:20',
            'role'     => 'required|in:admin,project_manager,programmer,governance,operational',
            'password' => 'required|string|min:8|confirmed',
        ]);

        User::create([
            'name'     => $validated['name'],
            'email'    => $validated['email'],
            'nik'      => $validated['nik'],
            'phone_wa' => $validated['phone_wa'],
            'role'     => $validated['role'],
            'password' => Hash::make($validated['password']),
        ]);

        return redirect()->route('users.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => ['required', 'string', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'nik'      => ['required', 'string', 'max:50', Rule::unique('users')->ignore($user->id)],
            'phone_wa' => 'nullable|string|max:20',
            'role'     => 'required|in:admin,project_manager,programmer,governance,operational',
        ]);

        $user->update($validated);

        return redirect()->route('users.index')->with('success', 'Profil user berhasil diperbarui.');
    }

    public function resetPassword(Request $request, User $user)
    {
        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('users.index')->with('success', 'Password user berhasil direset.');
    }
}
