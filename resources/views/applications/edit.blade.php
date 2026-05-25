<x-layouts.app :title="'Edit Aplikasi'">

    <div class="max-w-xl mx-auto">
        <a href="{{ route('applications.index') }}"
           class="inline-flex items-center gap-2 text-sm text-slate-400 hover:text-white mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
            </svg>
            Kembali
        </a>

        <div class="bg-slate-900 border border-slate-800 rounded-xl p-6 sm:p-8">
            <h2 class="text-lg font-bold text-white mb-6">Edit Aplikasi</h2>

            <form method="POST" action="{{ route('applications.update', $application) }}" class="space-y-5">
                @csrf @method('PATCH')

                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">
                        Nama Aplikasi <span class="text-red-400">*</span>
                    </label>
                    <input type="text" id="name" name="name" value="{{ old('name', $application->name) }}" required
                           class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="repo_url" class="block text-sm font-medium text-slate-300 mb-1.5">URL Repository</label>
                    <input type="url" id="repo_url" name="repo_url"
                           value="{{ old('repo_url', $application->repo_url) }}"
                           class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                  focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label for="description" class="block text-sm font-medium text-slate-300 mb-1.5">Deskripsi</label>
                    <textarea id="description" name="description" rows="3"
                              class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5
                                     focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y">{{ old('description', $application->description) }}</textarea>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <a href="{{ route('applications.index') }}"
                       class="px-5 py-2.5 text-sm text-slate-400 hover:text-white transition-colors">Batal</a>
                    <button type="submit"
                            class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>

</x-layouts.app>
