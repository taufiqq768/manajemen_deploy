<x-layouts.app>
    <x-slot name="title">IT Work Hub - Repository Doc</x-slot>

    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-file-text text-indigo-500"></i> Repository Dokumen
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola dan simpan dokumen-dokumen umum Divisi IT.</p>
            </div>
            <div>
                <button class="inline-flex items-center gap-2 px-4 py-2 bg-[#639922] hover:bg-[#3B6D11] text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <i class="ti ti-upload"></i> Upload Dokumen
                </button>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-[#F1EFE8] dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-list"></i> Daftar Dokumen
                </h3>
                <div class="flex gap-2">
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="Cari dokumen..." class="pl-9 pr-4 py-1.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-[#639922] outline-none transition-shadow w-64">
                    </div>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                        <tr>
                            <th class="px-4 py-3 w-12 text-center">#</th>
                            <th class="px-4 py-3">Nama Dokumen</th>
                            <th class="px-4 py-3 text-center">Jenis</th>
                            <th class="px-4 py-3 text-center">Versi</th>
                            <th class="px-4 py-3">Tanggal Upload</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        <!-- Mock Data -->
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 text-center font-medium">1</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Standard Operating Procedure - IT Dev</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">SOP</span>
                            </td>
                            <td class="px-4 py-3 text-center">v2.1</td>
                            <td class="px-4 py-3 text-xs">20 Juni 2026</td>
                            <td class="px-4 py-3 text-center">
                                <button class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors" title="Unduh">
                                    <i class="ti ti-download text-lg"></i>
                                </button>
                            </td>
                        </tr>
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-4 py-3 text-center font-medium">2</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">Template BRD Resmi 2026</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">BRD</span>
                            </td>
                            <td class="px-4 py-3 text-center">v1.0</td>
                            <td class="px-4 py-3 text-xs">15 Juni 2026</td>
                            <td class="px-4 py-3 text-center">
                                <button class="p-1.5 text-slate-400 hover:text-indigo-600 transition-colors" title="Unduh">
                                    <i class="ti ti-download text-lg"></i>
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-layouts.app>
