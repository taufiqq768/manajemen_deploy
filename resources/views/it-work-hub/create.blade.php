<x-layouts.app>
    <x-slot name="title">Tambah Project - IT Work Hub</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.longlist') }}"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Tambah Project Baru</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftarkan project pengembangan aplikasi baru
                    ke IT Work Hub.</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <form action="#" class="p-6 space-y-8">

                {{-- Bagian 1: Informasi Umum --}}
                <div class="space-y-4">
                    <h3
                        class="text-lg font-semibold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">
                        1. Informasi Umum</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Project <span
                                    class="text-red-500">*</span></label>
                            <input type="text"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Contoh: Aplikasi Absensi Wajah">
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian Singkat</label>
                            <textarea rows="3"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Deskripsi singkat tentang project..."></textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Priority</label>
                            <select
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                <option>High</option>
                                <option>Medium</option>
                                <option>Low</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Awal</label>
                            <select
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                <option>Not Started</option>
                                <option>Live</option>
                                <option>Hold</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Squad / Tim <span
                                    class="font-normal text-slate-400 text-xs">(Bisa multi-person)</span></label>
                            <div
                                class="flex flex-wrap items-center gap-1.5 p-1.5 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus-within:border-[#639922] focus-within:ring-1 focus-within:ring-[#639922]">
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-700">
                                    Tim Alpha
                                    <button type="button" class="text-slate-400 hover:text-red-500"><i
                                            class="ti ti-x"></i></button>
                                </span>
                                <span
                                    class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-700">
                                    Budi
                                    <button type="button" class="text-slate-400 hover:text-red-500"><i
                                            class="ti ti-x"></i></button>
                                </span>
                                <input type="text"
                                    class="flex-1 min-w-[120px] bg-transparent border-none focus:ring-0 text-sm p-0.5 outline-none"
                                    placeholder="Ketik nama...">
                            </div>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Business Process Owner
                                (BPO)</label>
                            <input type="text"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Divisi terkait">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Progress Awal
                                (%)</label>
                            <input type="number" min="0" max="100"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                value="0">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nomor Dokumen
                                BRD</label>
                            <input type="text"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Opsional">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Pain Point --}}
                <div class="space-y-4 pt-4">
                    <h3
                        class="text-lg font-semibold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">
                        2. Analisis Pain Point</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian Pain
                                Point</label>
                            <textarea rows="3"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Apa masalah yang ingin diselesaikan?"></textarea>
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Impact
                                (Dampak)</label>
                            <textarea rows="3"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Dampak dari pain point tersebut"></textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('it-work-hub.longlist') }}"
                        class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </a>
                    <button type="button" onclick="window.location.href='{{ route('it-work-hub.longlist') }}'"
                        class="px-5 py-2.5 bg-[#639922] hover:bg-[#3B6D11] text-white font-medium rounded-lg shadow-sm transition-colors">
                        Simpan Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>