<x-layouts.app>
    <x-slot name="title">Detail Project - IT Work Hub</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.longlist') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">IT Work Hub</h2>
                        <span class="px-2 py-1 rounded text-[10px] font-bold bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400">NOT STARTED</span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Pusat manajemen dan pemantauan project IT</p>
                </div>
                <div>
                    <a href="{{ route('it-work-hub.activities', 1) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        <i class="ti ti-activity"></i> Detail Aktivitas
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            {{-- Kolom Kiri: Info Umum --}}
            <div class="lg:col-span-2 space-y-6">
                
                {{-- Card Info Umum --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="ti ti-info-circle"></i> Informasi Project
                        </h3>
                    </div>
                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Squad / Tim</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">Squad Alpha</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">BPO</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">Divisi IT</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Priority</p>
                            <p class="font-medium text-sm text-red-600">High</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Dokumen BRD</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">-</p>
                        </div>
                        <div class="col-span-2">
                            <p class="text-xs text-slate-500 mb-1">Progress Keseluruhan</p>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="bg-[#639922] h-full" style="width: 15%"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">15%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Pain Point --}}
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="ti ti-alert-triangle"></i> Pain Point
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Uraian Masalah</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300">Data project terpencar di berbagai file Excel sehingga sulit dilacak statusnya secara real-time.</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Impact (Dampak)</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300">Reporting lambat, sering terjadi miskomunikasi terkait status fitur.</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-500 mb-1">PIC Pain Point</p>
                                <p class="text-sm text-slate-700 dark:text-slate-300">Budi Santoso</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Tanggal</p>
                                <p class="text-sm text-slate-700 dark:text-slate-300">01 Juni 2026</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bagian Bawah: PIR & Dokumen (Full Width) --}}
        <div class="space-y-6">
            
            {{-- Card Post Implementation Review (Tabel) --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ti ti-checklist"></i> Post Implementation Review
                    </h3>
                    <button class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md transition-colors">
                        + Tambah Review
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3">Uraian</th>
                                <th class="px-4 py-3 w-32">Tanggal</th>
                                <th class="px-4 py-3 w-48">File (Upload)</th>
                                <th class="px-4 py-3 w-32 text-center">Link</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm">Review paska go-live fase 1 menunjukkan performa stabil.</td>
                                <td class="px-4 py-3 text-xs">25 Jun 2026</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <i class="ti ti-file-text text-blue-500"></i>
                                        <a href="#" class="text-xs hover:underline truncate">PIR_Report.pdf</a>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300" title="Buka Link">
                                        <i class="ti ti-external-link text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-slate-500 italic text-center" colspan="4">Belum ada review tambahan.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Card Dokumen Pendukung (Tabel) --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ti ti-paperclip"></i> Dokumen Pendukung
                    </h3>
                    <button class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md transition-colors">
                        + Tambah Dokumen
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3">Uraian</th>
                                <th class="px-4 py-3 w-32">Tanggal</th>
                                <th class="px-4 py-3 w-48">File (Upload)</th>
                                <th class="px-4 py-3 w-32 text-center">Link</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm">Dokumen Spesifikasi Kebutuhan Sistem (SRS)</td>
                                <td class="px-4 py-3 text-xs">20 Jun 2026</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <i class="ti ti-file-text text-blue-500"></i>
                                        <a href="#" class="text-xs hover:underline truncate">SRS_IT_Work_Hub.docx</a>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="#" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300" title="Buka Link">
                                        <i class="ti ti-external-link text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-3 text-sm">Mockup Desain UI/UX Fase 1</td>
                                <td class="px-4 py-3 text-xs">22 Jun 2026</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-2">
                                        <i class="ti ti-file-text text-blue-500"></i>
                                        <a href="#" class="text-xs hover:underline truncate">Mockup_UI_Fase1.pdf</a>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="https://figma.com" target="_blank" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300" title="Buka Link Figma">
                                        <i class="ti ti-external-link text-lg"></i>
                                    </a>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-layouts.app>
