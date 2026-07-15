<x-layouts.app :title="'Edit Request Deploy'">

    <div class="max-w-2xl mx-auto">
        <a href="{{ route('deploy-requests.show', $deployRequest) }}"
           class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali ke Detail
        </a>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Revisi Request Deploy</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">
                Request akan kembali ke status <span class="text-yellow-500 dark:text-yellow-400 font-medium">Pending</span> setelah disimpan.
            </p>

            <form method="POST" action="{{ route('deploy-requests.update', $deployRequest) }}" enctype="multipart/form-data" class="space-y-5">
                @csrf
                @method('PATCH')

                @php
                    $selectedJenis = is_array(old('jenis', $deployRequest->jenis)) 
                        ? old('jenis', $deployRequest->jenis) 
                        : (is_string(old('jenis', $deployRequest->jenis)) ? json_decode(old('jenis', $deployRequest->jenis), true) : []);
                    if (!is_array($selectedJenis)) {
                        $selectedJenis = explode(',', str_replace(['[', ']', '"'], '', old('jenis', $deployRequest->jenis)));
                    }
                    $selectedJenis = array_map('trim', $selectedJenis);
                    
                    // Map old string values to new categories
                    if (in_array('Bug', $selectedJenis)) {
                        $selectedJenis[] = 'bug_fixing';
                    }
                    if (in_array('CR', $selectedJenis)) {
                        $selectedJenis[] = 'perubahan_kecil';
                    }

                    // Map release notes
                    $notes = is_array(old('release_notes', $deployRequest->release_notes)) 
                        ? old('release_notes', $deployRequest->release_notes) 
                        : (is_string(old('release_notes', $deployRequest->release_notes)) ? json_decode(old('release_notes', $deployRequest->release_notes), true) : []);
                    
                    if (!is_array($notes)) {
                        $legacyNotesStr = old('release_notes', $deployRequest->release_notes);
                        $notes = [];
                        if (in_array('bug_fixing', $selectedJenis)) {
                            $notes['bug_fixing'] = $legacyNotesStr;
                        } elseif (in_array('perubahan_besar', $selectedJenis)) {
                            $notes['perubahan_besar'] = $legacyNotesStr;
                        } else {
                            $notes['perubahan_kecil'] = $legacyNotesStr;
                        }
                    }
                @endphp

                @if($deployRequest->ticket_number)
                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Nomor Tiket
                    </label>
                    <input type="text" value="{{ $deployRequest->ticket_number }}" readonly disabled
                           class="w-full bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-slate-500 dark:text-slate-400 text-sm rounded-lg px-3 py-2.5 cursor-not-allowed font-mono">
                </div>
                @endif

                <div>
                    <label for="application_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Aplikasi <span class="text-red-500">*</span>
                    </label>
                    <select id="application_id" name="application_id" required
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($applications as $app)
                        <option value="{{ $app->id }}" data-version="{{ $app->version }}" {{ $deployRequest->application_id == $app->id ? 'selected' : '' }}>
                            {{ $app->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Versi / Release <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="version" name="version" readonly required
                               value="{{ old('version', $deployRequest->version) }}"
                               class="w-full bg-slate-100 dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-600 dark:text-slate-300 text-sm rounded-lg px-3 py-2.5
                                      focus:outline-none cursor-not-allowed font-mono">
                        <p class="text-[11px] text-slate-500 mt-1">Versi saat ini. Versi rilis akan otomatis naik setelah pengajuan disetujui Project Manager.</p>
                    </div>
                    <div>
                        <label for="scheduled_at" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Jadwal Deploy</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                               value="{{ old('scheduled_at', $deployRequest->scheduled_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                {{-- Kategori --}}
                <div>
                    <label for="kategori" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Kategori <span class="text-red-500">*</span>
                    </label>
                    <select id="kategori" name="kategori" required onchange="handleKategoriChange()"
                        class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('kategori') border-red-500 @enderror">
                        <option value="">-- Pilih Kategori --</option>
                        <option value="cr" {{ old('kategori', $deployRequest->kategori) == 'cr' ? 'selected' : '' }}>CR (Change Request)</option>
                        <option value="enhancement" {{ old('kategori', $deployRequest->kategori) == 'enhancement' ? 'selected' : '' }}>Enhancement</option>
                        <option value="bug_fixing" {{ old('kategori', $deployRequest->kategori) == 'bug_fixing' ? 'selected' : '' }}>Bug Fixing</option>
                    </select>
                    @error('kategori')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Jenis Versioning <span class="text-red-500">*</span>
                    </label>
                    <div class="space-y-3 p-4 bg-slate-50 dark:bg-slate-800 rounded-lg border border-slate-200 dark:border-slate-700">
                        {{-- Perubahan Besar --}}
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input id="jenis_besar" name="jenis[]" type="checkbox" value="perubahan_besar"
                                       {{ in_array('perubahan_besar', $selectedJenis) ? 'checked' : '' }}
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
                                       {{ in_array('perubahan_kecil', $selectedJenis) ? 'checked' : '' }}
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
                                       {{ in_array('bug_fixing', $selectedJenis) ? 'checked' : '' }}
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

                {{-- Release Notes --}}
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
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y @error('release_notes.perubahan_besar') border-red-500 @enderror">{{ $notes['perubahan_besar'] ?? '' }}</textarea>
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
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y @error('release_notes.perubahan_kecil') border-red-500 @enderror">{{ $notes['perubahan_kecil'] ?? '' }}</textarea>
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
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y @error('release_notes.bug_fixing') border-red-500 @enderror">{{ $notes['bug_fixing'] ?? '' }}</textarea>
                        @error('release_notes.bug_fixing')
                            <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div>
                    <label for="release_impact" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Dampak / Impact</label>
                    <textarea id="release_impact" name="release_impact" rows="3"
                              class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('release_impact', $deployRequest->release_impact) }}</textarea>
                </div>

                {{-- Documents --}}
                <div class="space-y-3">
                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300">
                        Dokumen Pendukung & Terkait
                    </label>
                    <div id="documents-container" class="space-y-4">
                        @forelse($deployRequest->documents as $idx => $doc)
                            <div class="document-item p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-lg space-y-3">
                                <input type="hidden" name="documents[{{ $idx }}][id]" value="{{ $doc->id }}">
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-2">
                                    <span class="document-header-title text-xs font-bold text-slate-500 dark:text-slate-400 tracking-wider">Dokumen Pendukung #{{ $idx + 1 }}</span>
                                    @if($idx > 0)
                                        <button type="button" onclick="removeDocumentRow(this)" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Hapus dokumen ini">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                                            </svg>
                                        </button>
                                    @endif
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nomor Dokumen Terkait <span class="text-slate-400 font-normal">(opsional)</span></label>
                                    <input type="text" name="documents[{{ $idx }}][number]" value="{{ old("documents.{$idx}.number", $doc->document_number) }}" placeholder="Contoh: DM-2026-X-001" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Unggah Dokumen Pendukung <span class="text-slate-400 font-normal">(opsional, max 2MB)</span></label>
                                    @if($doc->file_path)
                                        <div class="mb-2 text-xs text-slate-600 dark:text-slate-400">
                                            File saat ini: <a href="{{ Storage::url($doc->file_path) }}" target="_blank" class="text-indigo-500 underline font-medium">Lihat Dokumen</a>
                                        </div>
                                    @endif
                                    <input type="file" name="documents[{{ $idx }}][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        @empty
                            <div class="document-item p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-lg space-y-3">
                                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-2">
                                    <span class="document-header-title text-xs font-bold text-slate-500 dark:text-slate-400 tracking-wider">Dokumen Pendukung #1</span>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nomor Dokumen Terkait <span class="text-slate-400 font-normal">(opsional)</span></label>
                                    <input type="text" name="documents[0][number]" placeholder="Contoh: DM-2026-X-001" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Unggah Dokumen Pendukung <span class="text-slate-400 font-normal">(opsional, max 2MB)</span></label>
                                    <input type="file" name="documents[0][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                                </div>
                            </div>
                        @endforelse
                    </div>
                    <div class="pt-1">
                        <button type="button" onclick="addDocumentRow()" class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-medium rounded-lg transition-colors border border-slate-700">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                            Tambah Dokumen Lainnya
                        </button>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('deploy-requests.show', $deployRequest) }}"
                       class="px-5 py-2.5 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white transition-colors">Batal</a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan Revisi
                    </button>
                </div>
            </form>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateFormStates() {
            const appSelect = document.getElementById('application_id');
            const selectedOpt = appSelect.options[appSelect.selectedIndex];
            
            if (!selectedOpt || !selectedOpt.value) {
                document.getElementById('version').value = '';
                document.getElementById('version').placeholder = 'Pilih aplikasi & jenis versioning';
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
        
        function handleKategoriChange() {
            const kategori = document.getElementById('kategori').value;
            const cbBesar = document.getElementById('jenis_besar');
            const cbKecil = document.getElementById('jenis_kecil');
            const cbBug = document.getElementById('jenis_bug');

            if (kategori === 'cr' || kategori === 'enhancement') {
                cbBesar.disabled = false;
                cbKecil.disabled = false;
                
                cbBug.checked = false;
                cbBug.disabled = true;
            } else if (kategori === 'bug_fixing') {
                cbBesar.checked = false;
                cbBesar.disabled = true;
                
                cbKecil.checked = false;
                cbKecil.disabled = true;
                
                cbBug.disabled = false;
                cbBug.checked = true;
            } else {
                cbBesar.disabled = false;
                cbKecil.disabled = false;
                cbBug.disabled = false;
            }
            updateFormStates();
        }

        document.getElementById('application_id').addEventListener('change', updateFormStates);
        
        // Also trigger on checkbox changes
        document.getElementById('jenis_besar').addEventListener('change', updateFormStates);
        document.getElementById('jenis_kecil').addEventListener('change', updateFormStates);
        document.getElementById('jenis_bug').addEventListener('change', updateFormStates);
        
        // Run on page load — DOM is already ready since script is at end of body
        handleKategoriChange();
        
        // Show native picker on click of the scheduled_at input
        const scheduledInput = document.getElementById('scheduled_at');
        if (scheduledInput) {
            scheduledInput.addEventListener('click', function() {
                try {
                    this.showPicker();
                } catch (e) {}
            });
        }

        // Dynamic Document Rows
        let docIndex = {{ count($deployRequest->documents) ?: 1 }};
        function addDocumentRow() {
            const container = document.getElementById('documents-container');
            const items = container.querySelectorAll('.document-item');
            const nextNum = items.length + 1;
            
            const div = document.createElement('div');
            div.className = "document-item p-4 bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-800 rounded-lg space-y-3";
            div.innerHTML = `
                <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-2">
                    <span class="document-header-title text-xs font-bold text-slate-500 dark:text-slate-400 tracking-wider">Dokumen Pendukung #${nextNum}</span>
                    <button type="button" onclick="removeDocumentRow(this)" class="text-slate-400 hover:text-red-500 transition-colors p-1" title="Hapus dokumen ini">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/>
                        </svg>
                    </button>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Nomor Dokumen Terkait <span class="text-slate-400 font-normal">(opsional)</span></label>
                    <input type="text" name="documents[${docIndex}][number]" placeholder="Contoh: DM-2026-X-001" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-500 dark:text-slate-400 mb-1">Unggah Dokumen Pendukung <span class="text-slate-400 font-normal">(opsional, max 2MB)</span></label>
                    <input type="file" name="documents[${docIndex}][file]" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt" class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            `;
            container.appendChild(div);
            docIndex++;
        }
        function removeDocumentRow(btn) {
            btn.closest('.document-item').remove();
            
            // Recalculate document headers sequentially
            const container = document.getElementById('documents-container');
            const items = container.querySelectorAll('.document-item');
            items.forEach((item, idx) => {
                const header = item.querySelector('.document-header-title');
                if (header) {
                    header.textContent = `Dokumen Pendukung #${idx + 1}`;
                }
            });
        }
    </script>
    @endpush

</x-layouts.app>
