<x-layouts.app :title="'Profil Saya'">

    <div class="max-w-2xl mx-auto">
        <h2 class="text-xl font-bold text-slate-900 dark:text-white mb-1">Ganti Password</h2>
        <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
            Pastikan akun Anda menggunakan password yang panjang dan acak agar tetap aman.
        </p>

        @if (session('status') === 'password-updated')
            <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-600 dark:text-green-400 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Password berhasil diperbarui!
            </div>
        @endif

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 sm:p-8">
            <form method="post" action="{{ route('password.update') }}" class="space-y-5">
                @csrf
                @method('put')

                <div>
                    <label for="current_password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Password Saat Ini <span class="text-red-500">*</span>
                    </label>
                    <input type="password" id="current_password" name="current_password" required autocomplete="current-password"
                        class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @if($errors->updatePassword->has('current_password')) border-red-500 @endif">
                    @if($errors->updatePassword->has('current_password'))
                        <p class="mt-1 text-xs text-red-500">{{ $errors->updatePassword->first('current_password') }}</p>
                    @endif
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password" name="password" required autocomplete="new-password"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @if($errors->updatePassword->has('password')) border-red-500 @endif">
                        @if($errors->updatePassword->has('password'))
                            <p class="mt-1 text-xs text-red-500">{{ $errors->updatePassword->first('password') }}</p>
                        @endif
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @if($errors->updatePassword->has('password_confirmation'))
                            <p class="mt-1 text-xs text-red-500">{{ $errors->updatePassword->first('password_confirmation') }}</p>
                        @endif
                    </div>
                </div>

                <div class="flex items-center justify-end pt-4 mt-6 border-t border-slate-200 dark:border-slate-800">
                    <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan Password Baru
                    </button>
                </div>
            </form>
        </div>

        <div class="mt-8 p-4 bg-slate-50 dark:bg-slate-800/50 rounded-xl border border-slate-200 dark:border-slate-800/50">
            <h3 class="text-sm font-medium text-slate-800 dark:text-slate-200 mb-2 flex items-center gap-2">
                <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Informasi Akun
            </h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">
                Untuk mengubah nama lengkap, email, atau NIK Anda, silakan hubungi <strong class="font-medium text-slate-700 dark:text-slate-300">Administrator</strong>. Data identitas dikelola secara terpusat untuk menjaga validitas sistem.
            </p>
        </div>
    </div>

</x-layouts.app>
