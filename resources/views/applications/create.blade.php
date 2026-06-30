<x-layouts.app :title="'Tambah Aplikasi'">

    <div class="max-w-xl mx-auto">
        <a href="{{ route('applications.index') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white mb-6">Tambah Aplikasi Manual</h2>
            <p class="text-xs text-slate-500 -mt-4 mb-6">
                Aplikasi yang terdaftar di
                <span class="text-indigo-400">gup.ptpn1.co.id</span>
                akan otomatis tersinkron saat programmer membuka form pengajuan deploy.
            </p>

            <form method="POST" action="{{ route('applications.store') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Nama Aplikasi <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" required
                           placeholder="contoh: API Gateway, CRM System"
                           class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('name') border-red-500 @enderror">
                    @error('name') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="repo_url" class="block text-sm font-medium text-slate-300 mb-1.5">URL Repository</label>
                    <input type="url" id="repo_url" name="repo_url" value="{{ old('repo_url') }}"
                           placeholder="https://github.com/org/repo"
                           class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('repo_url') border-red-500 @enderror">
                    @error('repo_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="app_url" class="block text-sm font-medium text-slate-300 mb-1.5">URL Live / App</label>
                    <input type="url" id="app_url" name="app_url" value="{{ old('app_url') }}"
                           placeholder="https://app.example.com"
                           class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('app_url') border-red-500 @enderror">
                    @error('app_url') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="version" class="block text-sm font-medium text-slate-300 mb-1.5">Versi Aplikasi</label>
                    <input type="text" id="version" name="version" value="{{ old('version') }}"
                           placeholder="contoh: 1.0.0"
                           class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500
                                  @error('version') border-red-500 @enderror">
                    @error('version') <p class="mt-1 text-xs text-red-400">{{ $message }}</p> @enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-1.5">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              placeholder="Deskripsi singkat aplikasi..."
                              class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('description') }}</textarea>
                </div>

                <div>
                    <label for="pic_ids" class="block text-sm font-medium text-slate-300 mb-1.5">Pilih PIC (Programmer)</label>
                    <select id="pic_ids" name="pic_ids[]" multiple
                            class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500 min-h-[120px]">
                        @foreach($programmers as $programmer)
                            <option value="{{ $programmer->id }}" {{ in_array($programmer->id, old('pic_ids', [])) ? 'selected' : '' }}>
                                {{ $programmer->name }} ({{ $programmer->email }})
                            </option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-500 mt-1.5">Tahan Ctrl (Windows) / Cmd (Mac) untuk memilih lebih dari satu PIC.</p>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('applications.index') }}"
                       class="px-5 py-2.5 text-sm text-slate-400 hover:text-white transition-colors">Batal</a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
