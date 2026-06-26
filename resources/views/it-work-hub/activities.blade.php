<x-layouts.app>
    <x-slot name="title">Detail Aktivitas - IT Work Hub</x-slot>

    <div class="w-full px-4 2xl:px-8 mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.longlist') }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    IT Work Hub <span class="text-slate-400 font-normal text-lg">| Detail Aktivitas</span>
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola daftar fitur awal, change request (CR), dan pelaporan bug.</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            {{-- Tabs Header --}}
            <div class="flex border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 px-2 pt-2">
                <button id="tab-btn-fitur" onclick="switchTab('fitur')" class="px-5 py-3 text-sm font-semibold border-b-2 border-[#639922] text-[#639922] bg-white dark:bg-slate-900 rounded-t-lg transition-colors">
                    <i class="ti ti-list-check"></i> List Fitur Awal
                </button>
                <button id="tab-btn-cr" onclick="switchTab('cr')" class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    <i class="ti ti-rotate-clockwise"></i> Change Request
                </button>
                <button id="tab-btn-bug" onclick="switchTab('bug')" class="px-5 py-3 text-sm font-medium text-slate-500 hover:text-slate-700 dark:hover:text-slate-300 transition-colors">
                    <i class="ti ti-bug"></i> Bugs / Note
                </button>
            </div>

            {{-- Tab Content: List Fitur Awal --}}
            <div id="tab-content-fitur" class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-slate-800 dark:text-slate-200">Daftar Fitur yang Dikerjakan</h3>
                    <button onclick="addRow('tbody-fitur')" class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md shadow-sm transition-colors flex items-center gap-1">
                        <i class="ti ti-row-insert-bottom"></i> Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-[10px] sm:text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-y border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-2 py-3 w-8 text-center"></th>
                                <th class="px-2 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 min-w-[200px]">Nama Fitur</th>
                                <th class="px-2 py-3 w-32 text-center">Tanggal</th>
                                <th class="px-2 py-3 w-32 text-center">Deadline</th>
                                <th class="px-2 py-3 w-32 text-center">Tgl Penyesuaian</th>
                                <th class="px-2 py-3 min-w-[200px]">Keterangan</th>
                                <th class="px-2 py-3 w-40">PIC</th>
                                <th class="px-2 py-3 w-32 text-center">Link Dok.</th>
                                <th class="px-2 py-3 w-36 text-center">Status</th>
                                <th class="px-2 py-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-fitur" class="divide-y divide-slate-200 dark:divide-slate-800">
                            <!-- Mock Data Row 1 -->
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                                <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span class="number-text">1</span></td>
                                <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded" value="Membuat UI Mockup App Dev"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded" value="2026-06-20"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded" value="2026-06-25"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-red-500 px-1 py-1.5 rounded" value="2026-06-26"></td>
                                <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded" value="Revisi desain tabel"></td>
                                <td class="px-1 py-1">
                                    <div class="flex items-center gap-1 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 rounded px-1.5 py-1">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 text-[10px] font-semibold">Frontend</span>
                                        <input type="text" class="flex-1 bg-transparent border-none focus:ring-0 text-xs p-0" placeholder="...">
                                    </div>
                                </td>
                                <td class="px-1 py-1 text-center"><input type="url" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-indigo-600 px-2 py-1.5 rounded text-center" value="https://docs.google.com/test"></td>
                                <td class="px-1 py-1">
                                    <select class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold text-blue-700 px-2 py-1.5 rounded uppercase appearance-none cursor-pointer">
                                        <option value="Not Started">NOT STARTED</option>
                                        <option value="Programming" selected>PROGRAMMING</option>
                                        <option value="Tech Testing">TECH TESTING</option>
                                        <option value="Done">DONE</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
                            </tr>
                            <!-- Mock Data Row 2 -->
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                                <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span class="number-text">2</span></td>
                                <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded" value="Database Migration & Models"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded" value="2026-06-27"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded" value="2026-06-30"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded"></td>
                                <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded" value="-"></td>
                                <td class="px-1 py-1">
                                    <div class="flex items-center gap-1 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 rounded px-1.5 py-1">
                                        <span class="inline-flex items-center gap-1 px-1.5 py-0.5 rounded bg-slate-200 text-slate-700 text-[10px] font-semibold">Backend</span>
                                        <input type="text" class="flex-1 bg-transparent border-none focus:ring-0 text-xs p-0" placeholder="...">
                                    </div>
                                </td>
                                <td class="px-1 py-1 text-center"><input type="url" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-indigo-600 px-2 py-1.5 rounded text-center" placeholder="https://..."></td>
                                <td class="px-1 py-1">
                                    <select class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold text-slate-500 px-2 py-1.5 rounded uppercase appearance-none cursor-pointer">
                                        <option value="Not Started" selected>NOT STARTED</option>
                                        <option value="Programming">PROGRAMMING</option>
                                        <option value="Tech Testing">TECH TESTING</option>
                                        <option value="Done">DONE</option>
                                    </select>
                                </td>
                                <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab Content: Change Request --}}
            <div id="tab-content-cr" class="p-4 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-slate-800 dark:text-slate-200">Daftar Change Request (CR)</h3>
                    <button onclick="addRow('tbody-cr')" class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md shadow-sm transition-colors flex items-center gap-1">
                        <i class="ti ti-row-insert-bottom"></i> Tambah Baris CR
                    </button>
                </div>

                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-[10px] sm:text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-y border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-2 py-3 w-8 text-center"></th>
                                <th class="px-2 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 min-w-[200px]">Nama CR</th>
                                <th class="px-2 py-3 w-32 text-center">Tanggal</th>
                                <th class="px-2 py-3 w-32 text-center">Deadline</th>
                                <th class="px-2 py-3 w-32 text-center">Tgl Penyesuaian</th>
                                <th class="px-2 py-3 min-w-[200px]">Keterangan</th>
                                <th class="px-2 py-3 w-40">PIC</th>
                                <th class="px-2 py-3 w-32 text-center">Link Dok.</th>
                                <th class="px-2 py-3 w-36 text-center">Status</th>
                                <th class="px-2 py-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-cr" class="divide-y divide-slate-200 dark:divide-slate-800">
                            <tr class="empty-state">
                                <td class="px-3 py-8 text-center text-slate-400" colspan="11">Belum ada data Change Request. Klik "Tambah Baris CR" untuk menginput data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Tab Content: Bugs / Note --}}
            <div id="tab-content-bug" class="p-4 hidden">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-slate-800 dark:text-slate-200">Daftar Bugs & Note</h3>
                    <button onclick="addRow('tbody-bug')" class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md shadow-sm transition-colors flex items-center gap-1">
                        <i class="ti ti-row-insert-bottom"></i> Tambah Baris Bug
                    </button>
                </div>

                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-[10px] sm:text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-y border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-2 py-3 w-8 text-center"></th>
                                <th class="px-2 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 min-w-[200px]">Nama Bug/Note</th>
                                <th class="px-2 py-3 w-32 text-center">Tanggal</th>
                                <th class="px-2 py-3 w-32 text-center">Deadline</th>
                                <th class="px-2 py-3 w-32 text-center">Tgl Penyesuaian</th>
                                <th class="px-2 py-3 min-w-[200px]">Keterangan</th>
                                <th class="px-2 py-3 w-40">PIC</th>
                                <th class="px-2 py-3 w-32 text-center">Link Dok.</th>
                                <th class="px-2 py-3 w-36 text-center">Status</th>
                                <th class="px-2 py-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-bug" class="divide-y divide-slate-200 dark:divide-slate-800">
                            <tr class="empty-state">
                                <td class="px-3 py-8 text-center text-slate-400" colspan="11">Belum ada laporan Bug. Klik "Tambah Baris Bug" untuk menginput data.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
            
        </div>
    </div>

    <!-- Template for new row -->
    <template id="row-template">
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
            <td class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
            <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span class="number-text"></span></td>
            <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded" placeholder="Nama item..."></td>
            <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded"></td>
            <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded"></td>
            <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-red-500 px-1 py-1.5 rounded"></td>
            <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded" placeholder="Keterangan..."></td>
            <td class="px-1 py-1">
                <div class="flex items-center gap-1 bg-transparent hover:bg-slate-100 dark:hover:bg-slate-800 rounded px-1.5 py-1">
                    <input type="text" class="flex-1 bg-transparent border-none focus:ring-0 text-xs p-0" placeholder="Pilih PIC...">
                </div>
            </td>
            <td class="px-1 py-1 text-center"><input type="url" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-indigo-600 px-2 py-1.5 rounded text-center" placeholder="https://..."></td>
            <td class="px-1 py-1">
                <select class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold text-slate-500 px-2 py-1.5 rounded uppercase appearance-none cursor-pointer">
                    <option value="Not Started" selected>NOT STARTED</option>
                    <option value="Ureq Analysis">UREQ ANALYSIS</option>
                    <option value="Programming">PROGRAMMING</option>
                    <option value="Tech Testing">TECH TESTING</option>
                    <option value="UAT">UAT</option>
                    <option value="SIT">SIT</option>
                    <option value="Done">DONE</option>
                </select>
            </td>
            <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
        </tr>
    </template>

    <script>
        function switchTab(tab) {
            // Sembunyikan semua konten tab
            document.getElementById('tab-content-fitur').classList.add('hidden');
            document.getElementById('tab-content-cr').classList.add('hidden');
            document.getElementById('tab-content-bug').classList.add('hidden');
            
            // Reset gaya tombol tab (hilangkan status aktif)
            const btnFitur = document.getElementById('tab-btn-fitur');
            const btnCr = document.getElementById('tab-btn-cr');
            const btnBug = document.getElementById('tab-btn-bug');
            
            const inactiveClass = ['font-medium', 'text-slate-500', 'hover:text-slate-700', 'dark:hover:text-slate-300'];
            const activeClass = ['font-semibold', 'border-b-2', 'border-[#639922]', 'text-[#639922]', 'bg-white', 'dark:bg-slate-900', 'rounded-t-lg'];
            
            // Remove active classes
            [btnFitur, btnCr, btnBug].forEach(btn => {
                btn.classList.remove(...activeClass);
                btn.classList.add(...inactiveClass);
            });
            
            // Tampilkan konten tab yang dipilih dan beri gaya aktif
            document.getElementById('tab-content-' + tab).classList.remove('hidden');
            const activeBtn = document.getElementById('tab-btn-' + tab);
            activeBtn.classList.remove(...inactiveClass);
            activeBtn.classList.add(...activeClass);
        }

        function addRow(tbodyId) {
            const tbody = document.getElementById(tbodyId);
            
            // Hilangkan tulisan "Belum ada data" jika ada
            const emptyState = tbody.querySelector('.empty-state');
            if (emptyState) {
                emptyState.remove();
            }

            // Ambil template
            const template = document.getElementById('row-template');
            const newRow = template.content.cloneNode(true);
            
            // Hitung nomor urut terbaru
            const trs = tbody.querySelectorAll('tr:not(.empty-state)');
            const newIndex = trs.length + 1;
            newRow.querySelector('.number-text').textContent = newIndex;

            // Append baris baru
            tbody.appendChild(newRow);
        }

        function removeRow(btn) {
            const tr = btn.closest('tr');
            const tbody = tr.parentElement;
            tr.remove();

            // Update penomoran
            const remainingTrs = tbody.querySelectorAll('tr:not(.empty-state)');
            remainingTrs.forEach((row, index) => {
                row.querySelector('.number-text').textContent = index + 1;
            });

            // Munculkan empty state jika tabel kosong
            if (remainingTrs.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'empty-state';
                emptyRow.innerHTML = `<td class="px-3 py-8 text-center text-slate-400" colspan="11">Belum ada data. Klik "Tambah Baris" untuk menginput data.</td>`;
                tbody.appendChild(emptyRow);
            }
        }
    </script>
</x-layouts.app>
