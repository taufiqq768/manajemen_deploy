<x-layouts.app :title="'Manajemen Aplikasi'">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Manajemen Aplikasi</h2>
            <p class="text-sm text-slate-400 mt-0.5">Daftar aplikasi yang dapat di-deploy</p>
        </div>
        <a href="{{ route('applications.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Tambah Aplikasi
        </a>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-slate-500">
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-sm">Belum ada aplikasi terdaftar</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/60 text-slate-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama Aplikasi</th>
                        <th class="px-5 py-3 text-left">URL Live</th>
                        <th class="px-5 py-3 text-left">Repository</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($applications as $app)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-5 py-4">
                            <p class="font-medium text-white">{{ $app->name }}</p>
                            @if($app->description)
                            <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $app->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($app->app_url)
                            <a href="{{ $app->app_url }}" target="_blank"
                               class="text-emerald-400 hover:text-emerald-300 text-xs transition-colors truncate max-w-xs block">
                                {{ parse_url($app->app_url, PHP_URL_HOST) }}
                            </a>
                            @else
                            <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($app->repo_url)
                            <a href="{{ $app->repo_url }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 text-xs transition-colors truncate max-w-xs block">
                                {{ parse_url($app->repo_url, PHP_URL_HOST) }}…
                            </a>
                            @else
                            <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('applications.edit', $app) }}"
                                   class="text-slate-400 hover:text-white text-xs transition-colors">Edit</a>
                                <form method="POST" action="{{ route('applications.destroy', $app) }}"
                                      onsubmit="return confirm('Hapus aplikasi ini? Semua request terkait akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
        <div class="px-5 py-4 border-t border-slate-800">{{ $applications->links() }}</div>
        @endif
        @endif
    </div>

</x-layouts.app>
