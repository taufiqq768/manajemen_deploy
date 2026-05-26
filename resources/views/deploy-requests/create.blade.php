<x-layouts.app :title="'Ajukan Deploy Baru'">

    <div class="max-w-2xl mx-auto">
        {{-- Back --}}
        <a href="{{ route('deploy-requests.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Form Pengajuan Deploy</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">Isi semua informasi yang diperlukan untuk
                pengajuan ke environment Production.</p>

            @if($applications->isEmpty())
                <div class="flex flex-col items-center justify-center py-12 text-slate-400 dark:text-slate-500">
                    <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                    </svg>
                    <p class="text-sm">Belum ada aplikasi yang terdaftar di sistem.</p>
                    <p class="text-xs mt-1">Daftar aplikasi akan otomatis tersinkron dari API. Hubungi admin jika belum muncul.</p>
                </div>
            @else
                <form method="POST" action="{{ route('deploy-requests.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf

                    {{-- Aplikasi --}}
                    <div>
                        <label for="application_id"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Aplikasi <span class="text-red-500">*</span>
                        </label>
                        <select id="application_id" name="application_id" required class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                       focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent
                                       @error('application_id') border-red-500 @enderror">
                            <option value="">— Pilih Aplikasi —</option>
                            @foreach($applications as $app)
                                <option value="{{ $app->id }}" {{ old('application_id') == $app->id ? 'selected' : '' }}>
                                    {{ $app->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('application_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Versi & Jadwal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label for="version"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Versi / Release <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="version" name="version" value="{{ old('version') }}"
                                placeholder="contoh: 1.2.3 atau v2024.01" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                          focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono
                                          @error('version') border-red-500 @enderror">
                            @error('version')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div>
                            <label for="scheduled_at"
                                class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Jadwal Deploy
                            </label>
                            <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                                value="{{ old('scheduled_at') }}" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                          focus:outline-none focus:ring-2 focus:ring-indigo-500
                                          @error('scheduled_at') border-red-500 @enderror">
                            @error('scheduled_at')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    {{-- Release Notes --}}
                    <div>
                        <label for="release_notes"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Release Notes <span class="text-red-500">*</span>
                        </label>
                        <textarea id="release_notes" name="release_notes" rows="5" required
                            placeholder="Jelaskan perubahan yang akan di-deploy"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                         focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y
                                         @error('release_notes') border-red-500 @enderror">{{ old('release_notes') }}</textarea>
                        @error('release_notes')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Release Impact --}}
                    <div>
                        <label for="release_impact"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Dampak / Impact
                        </label>
                        <textarea id="release_impact" name="release_impact" rows="3"
                            placeholder="Sebutkan dampak yang mungkin terjadi (downtime, perubahan schema, dll)"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                         focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('release_impact') }}</textarea>
                    </div>

                    {{-- Document Support --}}
                    <div>
                        <label for="document_support"
                            class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Dokumen Pendukung <span class="text-xs text-slate-500">(opsional, max 2MB, format: pdf, doc, jpg, png, txt)</span>
                        </label>
                        <input type="file" id="document_support" name="document_support" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt"
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                         focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('document_support') border-red-500 @enderror">
                        @error('document_support')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Submit --}}
                    <div class="flex items-center justify-end gap-3 pt-2">
                        <a href="{{ route('deploy-requests.index') }}"
                            class="px-5 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors">
                            Batal
                        </a>
                        <button type="submit" class="inline-flex items-center gap-2 px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500
                                       text-white text-sm font-medium rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Ajukan Request
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>

</x-layouts.app>