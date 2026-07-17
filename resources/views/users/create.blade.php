<x-layouts.app :title="'Tambah User Baru'">

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('users.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Tambah User Baru</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Isi formulir berikut untuk mendaftarkan akun pengguna baru ke dalam sistem.</p>

            <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Nama Lengkap <span class="text-red-500">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                        class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('name') border-red-500 @enderror">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="email" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" required
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('email') border-red-500 @enderror">
                        @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="nik" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            NIK <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('nik') border-red-500 @enderror">
                        @error('nik')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="phone_wa" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Nomor WhatsApp <span class="text-xs text-slate-500">(opsional)</span>
                        </label>
                        <input type="text" id="phone_wa" name="phone_wa" value="{{ old('phone_wa') }}"
                            placeholder="08..."
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono @error('phone_wa') border-red-500 @enderror">
                        @error('phone_wa')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="role" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Role / Peran <span class="text-red-500">*</span>
                        </label>
                        <select id="role" name="role" required class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('role') border-red-500 @enderror">
                            <option value="programmer" {{ old('role') === 'programmer' ? 'selected' : '' }}>Programmer</option>
                            <option value="project_manager" {{ old('role') === 'project_manager' ? 'selected' : '' }}>Project Manager</option>
                            <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>Administrator</option>
                            <option value="governance" {{ old('role') === 'governance' ? 'selected' : '' }}>Governance</option>
                            <option value="operational" {{ old('role') === 'operational' ? 'selected' : '' }}>Operational</option>
                        </select>
                        @error('role')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="border-t border-slate-200 dark:border-slate-800 pt-5 mt-5">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-4">Pengaturan Password</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Password Baru <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password" name="password" required
                                class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('password') border-red-500 @enderror">
                            @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Konfirmasi Password <span class="text-red-500">*</span>
                            </label>
                            <input type="password" id="password_confirmation" name="password_confirmation" required
                                class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-4">
                    <a href="{{ route('users.index') }}"
                        class="px-5 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan User
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
