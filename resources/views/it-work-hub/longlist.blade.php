<x-layouts.app>
    <x-slot name="title">IT Work Hub - App Dev</x-slot>

    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-code text-indigo-500"></i> App Dev Longlist
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola dan pantau seluruh project IT pengembangan aplikasi.</p>
            </div>
            <div>
                <a href="{{ route('it-work-hub.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#639922] hover:bg-[#3B6D11] text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <i class="ti ti-plus"></i> Tambah Project
                </a>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500 uppercase">Total Project</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">24</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500 uppercase">Not Started</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">3</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-green-600 uppercase">Live</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-500 mt-1">12</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-purple-600 uppercase">Live w/ CR</p>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-500 mt-1">5</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-amber-600 uppercase">Live w/ Bug</p>
                <p class="text-2xl font-bold text-amber-700 dark:text-amber-500 mt-1">2</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-red-600 uppercase">Hold/Retired</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-500 mt-1">2</p>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-[#F1EFE8] dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-list"></i> Daftar Project Aktif
                </h3>
                <div class="flex gap-2">
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="Cari project..." class="pl-9 pr-4 py-1.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-[#639922] outline-none transition-shadow w-64">
                    </div>
                    <button class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                        <tr>
                            <th class="px-2 py-3 w-8 text-center"></th>
                            <th class="px-4 py-3 w-12 text-center">#</th>
                            <th class="px-4 py-3">Nama Project</th>
                            <th class="px-4 py-3">Uraian Singkat</th>
                            <th class="px-4 py-3 text-center">Priority</th>
                            <th class="px-4 py-3">Squad / Tim</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Progress</th>
                            <th class="px-4 py-3">BPO</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        <!-- Mock Data Row 1 -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-2 py-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                            <td class="px-4 py-3 text-center font-medium">1</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">IT Work Hub</td>
                            <td class="px-4 py-3 text-xs max-w-xs truncate" title="Pusat manajemen dan pemantauan project IT">Pusat manajemen dan pemantauan project IT</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">HIGH</span>
                            </td>
                            <td class="px-4 py-3 text-xs">Squad Alpha</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">NOT STARTED</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="bg-[#639922] h-full" style="width: 15%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">15%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs">Divisi IT</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('it-work-hub.show', 1) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-md transition-colors">
                                    Detail <i class="ti ti-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                        
                        <!-- Mock Data Row 2 -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-2 py-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                            <td class="px-4 py-3 text-center font-medium">2</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Manajemen Deploy</td>
                            <td class="px-4 py-3 text-xs max-w-xs truncate" title="Sistem pengajuan release ke production">Sistem pengajuan release ke production</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">MEDIUM</span>
                            </td>
                            <td class="px-4 py-3 text-xs">Squad Beta</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">LIVE</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="bg-green-500 h-full" style="width: 100%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">100%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs">Operasional</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('it-work-hub.show', 2) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-md transition-colors">
                                    Detail <i class="ti ti-arrow-right"></i>
                                </a>
                            </td>
                        </tr>

                        <!-- Mock Data Row 3 -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-2 py-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                            <td class="px-4 py-3 text-center font-medium">3</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">HRIS Dashboard</td>
                            <td class="px-4 py-3 text-xs max-w-xs truncate" title="Dashboard analitik kepegawaian">Dashboard analitik kepegawaian</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">HIGH</span>
                            </td>
                            <td class="px-4 py-3 text-xs">Squad Gamma</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400">LIVE WITH CR</span>
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="bg-purple-500 h-full" style="width: 90%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">90%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs">SDM</td>
                            <td class="px-4 py-3 text-center">
                                <a href="{{ route('it-work-hub.show', 3) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-md transition-colors">
                                    Detail <i class="ti ti-arrow-right"></i>
                                </a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500">
                <div>Menampilkan 1 hingga 3 dari 24 project</div>
                <div class="flex gap-1">
                    <button class="px-2 py-1 border border-slate-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 opacity-50 cursor-not-allowed">Prev</button>
                    <button class="px-2 py-1 border border-slate-300 dark:border-slate-700 rounded bg-indigo-50 dark:bg-indigo-900/20 text-indigo-600 dark:text-indigo-400 font-medium">1</button>
                    <button class="px-2 py-1 border border-slate-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700">2</button>
                    <button class="px-2 py-1 border border-slate-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700">3</button>
                    <button class="px-2 py-1 border border-slate-300 dark:border-slate-700 rounded bg-white dark:bg-slate-800 hover:bg-slate-50 dark:hover:bg-slate-700">Next</button>
                </div>
            </div>
        </div>
    </div>
</x-layouts.app>
