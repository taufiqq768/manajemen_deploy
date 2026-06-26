<div id="{{ $id }}" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
    <div class="min-h-screen px-4 text-center">
        {{-- Spacer untuk memposisikan modal di tengah --}}
        <span class="inline-block h-screen align-middle" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block w-full max-w-2xl p-6 my-8 text-left align-middle bg-white dark:bg-slate-900 shadow-xl rounded-2xl border border-slate-200 dark:border-slate-800">
            <div class="flex justify-between items-center border-b border-slate-200 dark:border-slate-800 pb-4 mb-5">
                <h3 class="text-lg font-bold text-slate-800 dark:text-white">{{ $title }}</h3>
                <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-300">
                    <i class="ti ti-x text-xl"></i>
                </button>
            </div>

            <form action="#" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">{{ $label }} <span class="text-red-500">*</span></label>
                        <input type="text" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]" placeholder="{{ $placeholder }}">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal <span class="text-red-500">*</span></label>
                        <input type="date" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">PIC <span class="font-normal text-slate-400 text-xs">(Bisa multi-person)</span></label>
                        <div class="flex flex-wrap items-center gap-1.5 p-1.5 w-full rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 focus-within:border-[#639922] focus-within:ring-1 focus-within:ring-[#639922]">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-medium border border-slate-200 dark:border-slate-700">
                                John Doe
                                <button type="button" class="text-slate-400 hover:text-red-500"><i class="ti ti-x"></i></button>
                            </span>
                            <input type="text" class="flex-1 min-w-[120px] bg-transparent border-none focus:ring-0 text-sm p-0.5 outline-none" placeholder="Ketik nama...">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Deadline</label>
                        <input type="date" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Deadline Penyesuaian</label>
                        <input type="date" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Link Dokumen Pendukung</label>
                        <input type="url" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]" placeholder="https://...">
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Keterangan</label>
                        <textarea rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]" placeholder="Catatan tambahan..."></textarea>
                    </div>

                    <div class="space-y-1 md:col-span-2">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Status</label>
                        <select class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                            <option>Not Started</option>
                            <option>Ureq Analysis</option>
                            <option>Programming</option>
                            <option>Tech Testing</option>
                            <option>UAT</option>
                            <option>SIT</option>
                            <option>Done</option>
                        </select>
                    </div>
                </div>

                <div class="mt-6 flex justify-end gap-3 border-t border-slate-200 dark:border-slate-800 pt-4">
                    <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="px-4 py-2 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </button>
                    <button type="button" onclick="document.getElementById('{{ $id }}').classList.add('hidden')" class="px-4 py-2 bg-[#639922] hover:bg-[#3B6D11] text-white text-sm font-medium rounded-lg transition-colors">
                        Simpan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
