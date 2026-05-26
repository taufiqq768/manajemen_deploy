<x-guest-layout>
    <!-- Session Status -->
    @if (session('status'))
        <div class="mb-6 p-4 rounded-xl bg-green-500/10 border border-green-500/20 text-sm text-green-600 dark:text-green-400">
            {{ session('status') }}
        </div>
    @endif

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf

        <!-- Email / NIK -->
        <div>
            <label for="login_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                Email / NIK
            </label>
            <input id="login_id" type="text" name="login_id" value="{{ old('login_id') }}" required autofocus autocomplete="username"
                   placeholder="Masukkan email atau NIK Anda"
                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-200 text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-colors @error('login_id') border-red-500 focus:border-red-500 focus:ring-red-500/50 @enderror">
            @error('login_id')
                <p class="mt-1.5 text-xs text-red-500 dark:text-red-400 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Password -->
        <div>
            <label for="password" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                Password
            </label>
            <input id="password" type="password" name="password" required autocomplete="current-password"
                   placeholder="••••••••"
                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-200 text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-colors @error('password') border-red-500 focus:border-red-500 focus:ring-red-500/50 @enderror">
            @error('password')
                <p class="mt-1.5 text-xs text-red-500 dark:text-red-400 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Captcha -->
        <div>
            <label for="captcha" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                Berapa hasil dari <strong>{{ session('captcha_question') }}</strong>
            </label>
            <input id="captcha" type="number" name="captcha" required
                   placeholder="Masukkan jawaban (angka)"
                   class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-900 dark:text-slate-200 text-sm rounded-lg px-4 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500 transition-colors @error('captcha') border-red-500 focus:border-red-500 focus:ring-red-500/50 @enderror">
            @error('captcha')
                <p class="mt-1.5 text-xs text-red-500 dark:text-red-400 font-medium">{{ $message }}</p>
            @enderror
        </div>

        <!-- Remember Me & Forgot Password -->
        <div class="flex items-center justify-between pt-1">
            <label for="remember_me" class="inline-flex items-center cursor-pointer group">
                <input id="remember_me" type="checkbox" name="remember" 
                       class="rounded border-slate-300 dark:border-slate-600 text-indigo-600 shadow-sm focus:ring-indigo-500/50 dark:bg-slate-800 dark:checked:bg-indigo-500 dark:checked:border-indigo-500 transition-colors">
                <span class="ms-2 text-sm text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-slate-300 transition-colors">Ingat saya</span>
            </label>

            @if (Route::has('password.request'))
                <a class="text-sm font-medium text-indigo-600 dark:text-indigo-400 hover:text-indigo-500 dark:hover:text-indigo-300 transition-colors" href="{{ route('password.request') }}">
                    Lupa password?
                </a>
            @endif
        </div>

        <div class="pt-4">
            <button type="submit" 
                    class="w-full flex justify-center items-center gap-2 py-2.5 px-4 rounded-lg shadow-sm shadow-indigo-500/20 text-sm font-semibold text-white bg-indigo-600 hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-all dark:focus:ring-offset-slate-900">
                <span>Masuk ke Sistem</span>
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/>
                </svg>
            </button>
        </div>
    </form>
</x-guest-layout>
