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

                <div>
                    <label for="application_id" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Aplikasi <span class="text-red-500">*</span>
                    </label>
                    <select id="application_id" name="application_id" required
                            class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                   focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        @foreach($applications as $app)
                        <option value="{{ $app->id }}" {{ $deployRequest->application_id == $app->id ? 'selected' : '' }}>
                            {{ $app->name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                    <div>
                        <label for="version" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                            Versi <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="version" name="version"
                               value="{{ old('version', $deployRequest->version) }}"
                               class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500 font-mono">
                    </div>
                    <div>
                        <label for="scheduled_at" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Jadwal Deploy</label>
                        <input type="datetime-local" id="scheduled_at" name="scheduled_at"
                               value="{{ old('scheduled_at', $deployRequest->scheduled_at?->format('Y-m-d\TH:i')) }}"
                               class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                      focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    </div>
                </div>

                <div>
                    <label for="release_notes" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Release Notes <span class="text-red-500">*</span>
                    </label>
                    <textarea id="release_notes" name="release_notes" rows="5" required
                              class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('release_notes', $deployRequest->release_notes) }}</textarea>
                </div>

                <div>
                    <label for="release_impact" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">Dampak / Impact</label>
                    <textarea id="release_impact" name="release_impact" rows="3"
                              class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('release_impact', $deployRequest->release_impact) }}</textarea>
                </div>

                <div>
                    <label for="document_support" class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1.5">
                        Dokumen Pendukung <span class="text-xs text-slate-500">(opsional, max 2MB, format: pdf, doc, jpg, png, txt)</span>
                    </label>
                    @if($deployRequest->document_support)
                        <div class="mb-2 text-sm text-slate-600 dark:text-slate-400">
                            File saat ini: <a href="{{ Storage::url($deployRequest->document_support) }}" target="_blank" class="text-indigo-500 underline font-medium">Lihat Dokumen</a>
                        </div>
                    @endif
                    <input type="file" id="document_support" name="document_support" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.txt"
                           class="w-full bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500 @error('document_support') border-red-500 @enderror">
                    @error('document_support')
                        <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                    @enderror
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

</x-layouts.app>
