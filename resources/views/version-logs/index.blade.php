<x-layouts.app :title="'Log Update Versi'">

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Log Update Versi</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                Riwayat sinkronisasi versi (API Get/Sync) dan pengiriman pembaruan versi (API Write/Push).
            </p>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 mb-6">
        <form method="GET" action="{{ route('version-logs.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-4 items-end">
            <div>
                <label for="application_id" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Aplikasi</label>
                <select name="application_id" id="application_id"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Aplikasi</option>
                    @foreach($applications as $app)
                        <option value="{{ $app->id }}" {{ request('application_id') == $app->id ? 'selected' : '' }}>{{ $app->name }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label for="type" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Tipe Aksi</label>
                <select name="type" id="type"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Tipe</option>
                    <option value="sync" {{ request('type') === 'sync' ? 'selected' : '' }}>API Get / Sync</option>
                    <option value="write" {{ request('type') === 'write' ? 'selected' : '' }}>API Write / Push</option>
                </select>
            </div>

            <div>
                <label for="status" class="block text-xs font-semibold text-slate-500 dark:text-slate-400 uppercase mb-2">Status</label>
                <select name="status" id="status"
                        class="w-full bg-slate-50 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="success" {{ request('status') === 'success' ? 'selected' : '' }}>Sukses</option>
                    <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>Gagal</option>
                </select>
            </div>

            <div class="flex gap-2">
                <button type="submit"
                        class="flex-1 inline-flex items-center justify-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-lg transition-colors">
                    Filter
                </button>
                @if(request()->hasAny(['application_id', 'type', 'status']))
                    <a href="{{ route('version-logs.index') }}"
                       class="inline-flex items-center justify-center px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-semibold rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                        Reset
                    </a>
                @endif
            </div>
        </form>
    </div>

    {{-- Content Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
        @if($logs->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-slate-400 dark:text-slate-500">
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-sm">Tidak ada log update versi yang ditemukan.</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Aplikasi</th>
                        <th class="px-5 py-3 text-left">Tipe Aksi</th>
                        <th class="px-5 py-3 text-left">Pembaruan Versi</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-left">Detail Pesan</th>
                        <th class="px-5 py-3 text-left">Waktu</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($logs as $log)
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $log->application->name }}</div>
                        </td>
                        <td class="px-5 py-4">
                            @if($log->type === 'write')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-emerald-100 text-emerald-800 dark:bg-emerald-500/20 dark:text-emerald-400">
                                    API Write / Push
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-md text-xs font-semibold bg-sky-100 text-sky-800 dark:bg-sky-500/20 dark:text-sky-400">
                                    API Get / Sync
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 font-mono text-xs">
                            @if($log->type === 'write')
                                <span class="text-slate-500 dark:text-slate-400">{{ $log->old_version ?: '—' }}</span>
                                <span class="mx-1 text-slate-400">→</span>
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $log->new_version ?: '—' }}</span>
                            @else
                                <span class="font-semibold text-slate-900 dark:text-white">{{ $log->new_version ?: '—' }}</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($log->status === 'success')
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-green-100 text-green-800 dark:bg-green-500/20 dark:text-green-400">
                                    Sukses
                                </span>
                            @else
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-semibold bg-red-100 text-red-800 dark:bg-red-500/20 dark:text-red-400">
                                    Gagal
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 max-w-xs truncate text-xs text-slate-600 dark:text-slate-400" title="{{ $log->message }}">
                            {{ $log->message ?: 'Tidak ada pesan detail.' }}
                        </td>
                        <td class="px-5 py-4 text-xs text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ $log->created_at->addHours(7)->format('d M Y, H:i') }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($logs->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
            {{ $logs->links() }}
        </div>
        @endif
        @endif
    </div>

</x-layouts.app>
