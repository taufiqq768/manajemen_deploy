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
                    <p class="text-xs mt-1">Daftar aplikasi akan otomatis tersinkron dari API. Hubungi admin jika belum
                        muncul.</p>
                </div>
            @else
                <form method="POST" action="{{ route('deploy-requests.store') }}" enctype="multipart/form-data"
                    class="space-y-5">
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
                                <option value="{{ $app->id }}" data-version="{{ $app->version }}" {{ old('application_id') == $app->id ? 'selected' : '' }}>
                                    {{ $app->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('application_id')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Jenis Request --}}
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Jenis Request <span class="text-red-500">*</span>
                        </label>
                        <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                            {{-- Perubahan Besar --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="jenis_besar" name="jenis[]" type="checkbox" value="perubahan_besar"
                                           {{ is_array(old('jenis')) && in_array('perubahan_besar', old('jenis')) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 bg-white dark:bg-slate-800"
                                           onchange="updateFormStates()">
                                </div>
                                <div class="ml-3 text-sm flex items-center gap-1.5">
                                    <label for="jenis_besar" class="font-medium text-slate-700 dark:text-slate-200 cursor-pointer">Perubahan Besar</label>
                                    <div class="relative group inline-block">
                                        <svg class="w-3.5 h-3.5 text-slate-400 cursor-pointer hover:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 bg-slate-950 text-slate-200 text-xs rounded-lg p-2.5 shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-10 border border-slate-800">
                                            Perubahan besar mencakup perubahan arsitektur utama, proses bisnis inti, fitur mayor baru, atau perubahan skema database major.
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-slate-950"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Perubahan Kecil --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="jenis_kecil" name="jenis[]" type="checkbox" value="perubahan_kecil"
                                           {{ is_array(old('jenis')) && in_array('perubahan_kecil', old('jenis')) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 bg-white dark:bg-slate-800"
                                           onchange="updateFormStates()">
                                </div>
                                <div class="ml-3 text-sm flex items-center gap-1.5">
                                    <label for="jenis_kecil" class="font-medium text-slate-700 dark:text-slate-200 cursor-pointer">Perubahan Kecil</label>
                                    <div class="relative group inline-block">
                                        <svg class="w-3.5 h-3.5 text-slate-400 cursor-pointer hover:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 bg-slate-950 text-slate-200 text-xs rounded-lg p-2.5 shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-10 border border-slate-800">
                                            Perubahan kecil mencakup penambahan fitur minor, perbaikan tampilan antarmuka (UI/UX), penyesuaian alur kerja minor, atau optimasi performa.
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-slate-950"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Bug Fixing --}}
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="jenis_bug" name="jenis[]" type="checkbox" value="bug_fixing"
                                           {{ is_array(old('jenis')) && in_array('bug_fixing', old('jenis')) ? 'checked' : '' }}
                                           class="w-4 h-4 text-indigo-600 border-slate-300 dark:border-slate-700 rounded focus:ring-indigo-500 bg-white dark:bg-slate-800"
                                           onchange="updateFormStates()">
                                </div>
                                <div class="ml-3 text-sm flex items-center gap-1.5">
                                    <label for="jenis_bug" class="font-medium text-slate-700 dark:text-slate-200 cursor-pointer">Bug Fixing</label>
                                    <div class="relative group inline-block">
                                        <svg class="w-3.5 h-3.5 text-slate-400 cursor-pointer hover:text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <div class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 w-64 bg-slate-950 text-slate-200 text-xs rounded-lg p-2.5 shadow-xl opacity-0 group-hover:opacity-100 pointer-events-none transition-opacity duration-200 z-10 border border-slate-800">
                                            Bug Fixing mencakup perbaikan galat/error, crash sistem, penambalan celah keamanan (security patches), atau pemulihan bug fitur.
                                            <div class="absolute top-full left-1/2 transform -translate-x-1/2 -mt-1 border-4 border-transparent border-t-slate-950"></div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @error('jenis')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Versi & Jadwal --}}
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                                Versi / Release <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="version" name="version" value="{{ old('version') }}" readonly required
                                placeholder="Pilih aplikasi" class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm rounded-lg px-3 py-2.5
                                              focus:outline-none cursor-not-allowed font-mono
                                              @error('version') border-red-500 @enderror">
                            <p class="text-[11px] text-slate-500 mt-1">Versi saat ini. Naik otomatis setelah disetujui PM.</p>
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

                    {{-- Release Notes (dynamic per jenis) --}}
                    <div id="release_notes_section" class="space-y-4 hidden">
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                            Catatan Rilis (Release Notes) <span class="text-red-500">*</span>
                        </label>
                        
                        <div id="notes_besar_container" class="hidden space-y-1.5">
                            <label for="release_notes_besar" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Catatan Rilis: Perubahan Besar <span class="text-red-500">*</span>
                            </label>
                            <textarea id="release_notes_besar" name="release_notes[perubahan_besar]" rows="3"
                                placeholder="Jelaskan detail perubahan besar yang dilakukan..."
                                class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y @error('release_notes.perubahan_besar') border-red-500 @enderror">{{ old('release_notes.perubahan_besar') }}</textarea>
                            @error('release_notes.perubahan_besar')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="notes_kecil_container" class="hidden space-y-1.5">
                            <label for="release_notes_kecil" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Catatan Rilis: Perubahan Kecil <span class="text-red-500">*</span>
                            </label>
                            <textarea id="release_notes_kecil" name="release_notes[perubahan_kecil]" rows="3"
                                placeholder="Jelaskan detail perubahan kecil / fitur minor..."
                                class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y @error('release_notes.perubahan_kecil') border-red-500 @enderror">{{ old('release_notes.perubahan_kecil') }}</textarea>
                            @error('release_notes.perubahan_kecil')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <div id="notes_bug_container" class="hidden space-y-1.5">
                            <label for="release_notes_bug" class="block text-xs font-semibold text-slate-500 dark:text-slate-400">
                                Catatan Rilis: Bug Fixing <span class="text-red-500">*</span>
                            </label>
                            <textarea id="release_notes_bug" name="release_notes[bug_fixing]" rows="3"
                                placeholder="Jelaskan detail perbaikan bug / error..."
                                class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y @error('release_notes.bug_fixing') border-red-500 @enderror">{{ old('release_notes.bug_fixing') }}</textarea>
                            @error('release_notes.bug_fixing')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>
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
                            Dokumen Pendukung <span class="text-xs text-slate-500">(opsional, max 2MB, format: pdf, doc,
                                jpg, png, txt)</span>
                        </label>
                        <input type="file" id="document_support" name="document_support"
                            accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt"
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

    @push('scripts')
    <script>
        function updateFormStates() {
            const appSelect = document.getElementById('application_id');
            const selectedOpt = appSelect.options[appSelect.selectedIndex];
            
            if (!selectedOpt || !selectedOpt.value) {
                document.getElementById('version').value = '';
                document.getElementById('version').placeholder = 'Pilih aplikasi';
                document.getElementById('release_notes_section').classList.add('hidden');
                return;
            }
            
            let baseVersion = selectedOpt.dataset.version || '0.0.0';
            baseVersion = baseVersion.trim();
            
            if (baseVersion === '' || baseVersion === '—') {
                baseVersion = '0.0.0';
            }
            
            // Display base version as-is (read-only), bumping will happen on approval
            document.getElementById('version').value = baseVersion;
            
            const isBesar = document.getElementById('jenis_besar').checked;
            const isKecil = document.getElementById('jenis_kecil').checked;
            const isBug = document.getElementById('jenis_bug').checked;
            
            // Dynamic Release Notes section visibility
            if (isBesar || isKecil || isBug) {
                document.getElementById('release_notes_section').classList.remove('hidden');
            } else {
                document.getElementById('release_notes_section').classList.add('hidden');
            }
            
            // Perubahan Besar notes
            if (isBesar) {
                document.getElementById('notes_besar_container').classList.remove('hidden');
                document.getElementById('release_notes_besar').required = true;
            } else {
                document.getElementById('notes_besar_container').classList.add('hidden');
                document.getElementById('release_notes_besar').required = false;
            }
            
            // Perubahan Kecil notes
            if (isKecil) {
                document.getElementById('notes_kecil_container').classList.remove('hidden');
                document.getElementById('release_notes_kecil').required = true;
            } else {
                document.getElementById('notes_kecil_container').classList.add('hidden');
                document.getElementById('release_notes_kecil').required = false;
            }
            
            // Bug Fixing notes
            if (isBug) {
                document.getElementById('notes_bug_container').classList.remove('hidden');
                document.getElementById('release_notes_bug').required = true;
            } else {
                document.getElementById('notes_bug_container').classList.add('hidden');
                document.getElementById('release_notes_bug').required = false;
            }
        }
        
        document.getElementById('application_id').addEventListener('change', updateFormStates);
        
        // Also trigger on checkbox changes
        document.getElementById('jenis_besar').addEventListener('change', updateFormStates);
        document.getElementById('jenis_kecil').addEventListener('change', updateFormStates);
        document.getElementById('jenis_bug').addEventListener('change', updateFormStates);
        
        // Run on page load — DOM is already ready since script is at end of body
        updateFormStates();
    </script>
    @endpush

</x-layouts.app>