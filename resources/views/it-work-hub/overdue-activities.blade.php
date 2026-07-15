<x-layouts.app title="Aktivitas Terlambat - IT Work Hub">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('it-work-hub.dashboard') }}" class="text-sm text-slate-500 hover:text-indigo-600 transition-colors">IT Work Hub</a>
                    <i class="ti ti-chevron-right text-slate-400 text-xs"></i>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Aktivitas Terlambat</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="ti ti-alert-circle text-red-500"></i> Daftar Aktivitas Terlambat
                </h1>
            </div>
            
            <div>
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400 text-sm font-semibold rounded-lg">
                    Total: {{ $overdueActivities->count() }}
                </span>
            </div>
        </div>

        <!-- List -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Nama Aktivitas</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Project / Task Induk</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-32">Fitur</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-36">Jatuh Tempo</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-36">Keterlambatan</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($overdueActivities as $act)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $act->name }}</div>
                            </td>
                            <td class="px-4 py-3">
                                <div class="text-sm text-slate-600 dark:text-slate-400 truncate max-w-xs" title="{{ $act->parent_name }}">{{ $act->parent_name }}</div>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($act->feature === 'App Dev')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-indigo-100 text-indigo-700 dark:bg-indigo-500/20 dark:text-indigo-400">APP DEV</span>
                                @elseif($act->feature === 'Non App')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400">NON APP</span>
                                @else
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-emerald-100 text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-400">GOVERNANCE</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center text-xs text-slate-600 dark:text-slate-400">
                                {{ $act->due_date->format('d M Y') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="text-xs font-bold text-red-600 dark:text-red-400">{{ $act->days_overdue }} Hari</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ $act->action_url }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-md transition-colors" title="Lihat Detail Aktivitas">
                                    <i class="ti ti-external-link"></i> Detail
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-4 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-600 dark:text-emerald-400 rounded-full flex items-center justify-center mb-4">
                                        <i class="ti ti-checks text-3xl"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-800 dark:text-slate-200">Luar Biasa!</h3>
                                    <p class="text-slate-500 dark:text-slate-400 mt-1 text-sm">Tidak ada aktivitas yang terlambat saat ini.</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
