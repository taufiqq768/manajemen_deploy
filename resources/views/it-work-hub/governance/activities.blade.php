<x-layouts.app>
    <x-slot name="title">Detail Aktivitas Governance - IT Work Hub</x-slot>

    @push('scripts')
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .ts-control {
            border: none;
            background: transparent;
            padding: 4px 6px;
            font-size: 10px;
            border-radius: 4px;
            box-shadow: none;
        }
        .ts-control:hover {
            background-color: transparent;
            border-color: #cbd5e1;
        }
        .ts-control.focus {
            box-shadow: 0 0 0 1px #639922;
            background-color: white;
            border-color: #639922;
        }
        .dark .ts-control.focus {
            background-color: #1e293b;
        }
        .ts-wrapper.multi .ts-control > div {
            background: #e2e8f0;
            color: #475569;
            border-radius: 4px;
            padding: 1px 5px;
            font-weight: 500;
        }
        .dark .ts-wrapper.multi .ts-control > div {
            background: #334155;
            color: #cbd5e1;
            border: 1px solid #475569;
        }
        .ts-dropdown {
            font-size: 11px;
            border-radius: 6px;
            box-shadow: 0 4px 6px -1px rgb(0 0 0 / 0.1);
        }
        .dark .ts-dropdown {
            background: #1e293b;
            color: #f8fafc;
            border-color: #334155;
        }
        .dark .ts-dropdown .option:hover, .dark .ts-dropdown .option.active {
            background-color: #334155;
            color: white;
        }
    </style>
    @endpush

    <div class="w-full px-4 2xl:px-8 mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.governance.show', $gov->id) }}" class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        {{ $gov->name }} <span class="text-slate-400 font-normal text-lg">| Detail Aktivitas</span>
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $gov->description ?? '-' }}</p>
                </div>
                <button onclick="saveAllActivities()" id="btn-save-all" class="px-5 py-2.5 bg-[#639922] hover:bg-opacity-90 text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="ti ti-device-floppy"></i> Simpan Semua Perubahan
                </button>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            {{-- Tab Content: Aktivitas --}}
            <div id="tab-content-aktivitas" class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-slate-800 dark:text-slate-200">Daftar Aktivitas</h3>
                    <button onclick="addRow('tbody-aktivitas')" class="px-3 py-1.5 bg-[#639922] hover:bg-opacity-90 text-white text-xs font-medium rounded-md shadow-sm transition-colors flex items-center gap-1">
                        <i class="ti ti-row-insert-bottom"></i> Tambah Baris
                    </button>
                </div>

                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-[10px] sm:text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-y border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-2 py-3 w-8 text-center"></th>
                                <th class="px-2 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 min-w-[200px]">Nama Aktivitas</th>
                                <th class="px-2 py-3 w-32 text-center">Tanggal</th>
                                <th class="px-2 py-3 w-32 text-center">Deadline</th>
                                <th class="px-2 py-3 w-32 text-center">Tgl Penyesuaian</th>
                                <th class="px-2 py-3 min-w-[200px]">Keterangan</th>
                                <th class="px-2 py-3 w-40">PIC</th>
                                <th class="px-2 py-3 w-48 text-center">Status & Progress</th>
                                <th class="px-2 py-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-aktivitas" class="divide-y divide-slate-200 dark:divide-slate-800">
                            @php $activitiesList = $gov->activities->sortBy('sort_order'); @endphp
                            @forelse($activitiesList as $index => $activity)
                            <tr data-id="{{ $activity->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                                <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span class="number-text">{{ $loop->iteration }}</span></td>
                                <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded input-name" value="{{ $activity->name }}"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded input-start-date" value="{{ $activity->start_date ? \Carbon\Carbon::parse($activity->start_date)->format('Y-m-d') : '' }}"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded input-end-date" value="{{ $activity->deadline ? \Carbon\Carbon::parse($activity->deadline)->format('Y-m-d') : '' }}"></td>
                                <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-red-500 px-1 py-1.5 rounded input-adjusted-date" value="{{ $activity->adjustment_date ? \Carbon\Carbon::parse($activity->adjustment_date)->format('Y-m-d') : '' }}"></td>
                                <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded input-desc" value="{{ $activity->notes }}"></td>
                                <td class="px-1 py-1" style="min-width: 200px;">
                                    <select multiple class="input-pics w-full" placeholder="Pilih PIC...">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $activity->pics->contains('id', $user->id) ? 'selected' : '' }}>{{ $user->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-1 py-1">
                                    <div class="flex items-center gap-1">
                                        <select onchange="updateStatusColor(this)" class="status-dropdown w-2/3 bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold px-1 py-1.5 rounded uppercase appearance-none cursor-pointer">
                                            <option value="Not Started" {{ $activity->status == 'Not Started' ? 'selected' : '' }}>NOT STARTED</option>
                                            <option value="On Progress" {{ $activity->status == 'On Progress' ? 'selected' : '' }}>ON PROGRESS</option>
                                            <option value="Hold" {{ $activity->status == 'Hold' ? 'selected' : '' }}>HOLD</option>
                                            <option value="Done" {{ $activity->status == 'Done' ? 'selected' : '' }}>DONE</option>
                                        </select>
                                        <div class="w-1/3 relative">
                                            <input type="number" min="0" max="100" class="input-progress w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded text-right pr-4" value="{{ $activity->progress ?? 0 }}">
                                            <span class="absolute right-1 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">%</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
                            </tr>
                            @empty
                            <tr class="empty-state">
                                <td class="px-3 py-8 text-center text-slate-400" colspan="10">Belum ada data Aktivitas. Klik "Tambah Baris" untuk menginput data.</td>
                            </tr>
                            @endforelse
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
            <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded input-name" placeholder="Nama item..."></td>
            <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded input-start-date"></td>
            <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded input-end-date"></td>
            <td class="px-1 py-1 text-center"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs text-red-500 px-1 py-1.5 rounded input-adjusted-date"></td>
            <td class="px-1 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded input-desc" placeholder="Keterangan..."></td>
            <td class="px-1 py-1" style="min-width: 200px;">
                <select multiple class="input-pics w-full" placeholder="Pilih PIC...">
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-1 py-1">
                <div class="flex items-center gap-1">
                    <select onchange="updateStatusColor(this)" class="status-dropdown text-slate-600 w-2/3 bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold px-1 py-1.5 rounded uppercase appearance-none cursor-pointer">
                        <option value="Not Started" class="text-slate-600" selected>NOT STARTED</option>
                        <option value="On Progress" class="text-blue-500">ON PROGRESS</option>
                        <option value="Hold" class="text-red-500">HOLD</option>
                        <option value="Done" class="text-green-500">DONE</option>
                    </select>
                    <div class="w-1/3 relative">
                        <input type="number" min="0" max="100" class="input-progress w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded text-right pr-4" value="0" readonly>
                        <span class="absolute right-1 top-1/2 -translate-y-1/2 text-xs text-slate-400 font-medium">%</span>
                    </div>
                </div>
            </td>
            <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
        </tr>
    </template>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        function updateStatusColor(select) {
            select.classList.remove('text-red-600', 'text-red-500', 'text-orange-500', 'text-amber-500', 'text-blue-500', 'text-indigo-500', 'text-teal-500', 'text-green-500', 'text-[#639922]', 'text-slate-600', 'text-slate-500');
            
            const progressInput = select.closest('td').querySelector('.input-progress');

            switch (select.value) {
                case 'Not Started': 
                    select.classList.add('text-slate-600'); 
                    if(progressInput) { 
                        progressInput.value = 0; 
                        progressInput.readOnly = true; 
                        progressInput.classList.add('text-slate-400', 'bg-slate-50');
                    }
                    break;
                case 'On Progress': 
                    select.classList.add('text-blue-500'); 
                    if(progressInput) { 
                        progressInput.readOnly = false; 
                        progressInput.classList.remove('text-slate-400', 'bg-slate-50');
                    }
                    break;
                case 'Hold': 
                    select.classList.add('text-red-500'); 
                    if(progressInput) { 
                        progressInput.readOnly = true; 
                        progressInput.classList.add('text-slate-400', 'bg-slate-50');
                    }
                    break;
                case 'Done': 
                    select.classList.add('text-green-500'); 
                    if(progressInput) { 
                        progressInput.value = 100; 
                        progressInput.readOnly = true; 
                        progressInput.classList.add('text-slate-400', 'bg-slate-50');
                    }
                    break;
                default: select.classList.add('text-slate-500'); break;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-dropdown').forEach(updateStatusColor);

            const el = document.getElementById('tbody-aktivitas');
            if(el) {
                new Sortable(el, {
                    handle: '.cursor-move',
                    animation: 150,
                    onEnd: function() {
                        const rows = el.querySelectorAll('tr:not(.empty-state)');
                        rows.forEach((row, idx) => {
                            row.querySelector('.number-text').textContent = idx + 1;
                        });
                    }
                });
            }

            // Init Tom Select for existing rows
            document.querySelectorAll('.input-pics').forEach(el => {
                new TomSelect(el, {
                    plugins: ['remove_button'],
                    hideSelected: true,
                    placeholder: "Pilih PIC..."
                });
            });
        });

        async function saveAllActivities() {
            const btn = document.getElementById('btn-save-all');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-loader animate-spin"></i> Menyimpan...';
            btn.disabled = true;

            const activities = [];
            const tbody = document.getElementById('tbody-aktivitas');
            const rows = tbody.querySelectorAll('tr:not(.empty-state)');
            
            rows.forEach((row, index) => {
                const activity = {
                    id: row.dataset.id || null,
                    sort_order: index + 1,
                    name: row.querySelector('.input-name').value,
                    start_date: row.querySelector('.input-start-date').value,
                    deadline: row.querySelector('.input-end-date').value,
                    adjustment_date: row.querySelector('.input-adjusted-date').value,
                    notes: row.querySelector('.input-desc').value,
                    pics: Array.from(row.querySelector('.input-pics').selectedOptions).map(opt => opt.value),
                    status: row.querySelector('.status-dropdown').value,
                    progress: parseInt(row.querySelector('.input-progress').value) || 0
                };
                if (activity.name.trim() !== '') {
                    activities.push(activity);
                }
            });

            try {
                const response = await fetch("{{ route('it-work-hub.governance.activities.save', $gov->id) }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ activities })
                });

                const result = await response.json();
                if (result.success) {
                    btn.innerHTML = '<i class="ti ti-check"></i> Tersimpan';
                    btn.classList.replace('bg-[#639922]', 'bg-blue-600');
                    setTimeout(() => window.location.reload(), 1000);
                } else {
                    throw new Error('Save failed');
                }
            } catch (error) {
                console.error(error);
                btn.innerHTML = '<i class="ti ti-alert-circle"></i> Gagal';
                btn.classList.replace('bg-[#639922]', 'bg-red-600');
                setTimeout(() => {
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                    btn.classList.replace('bg-red-600', 'bg-[#639922]');
                }, 2000);
            }
        }



        function addRow(tbodyId) {
            const tbody = document.getElementById(tbodyId);
            const emptyState = tbody.querySelector('.empty-state');
            if (emptyState) emptyState.remove();

            const template = document.getElementById('row-template');
            const newRow = template.content.cloneNode(true);
            
            const trs = tbody.querySelectorAll('tr:not(.empty-state)');
            newRow.querySelector('.number-text').textContent = trs.length + 1;
            
            const trElement = document.createElement('tr');
            trElement.className = newRow.children[0].className;
            trElement.innerHTML = newRow.children[0].innerHTML;
            
            tbody.appendChild(trElement);
            
            // Init TomSelect on the newly appended row
            const newSelect = trElement.querySelector('.input-pics');
            if(newSelect) {
                new TomSelect(newSelect, {
                    plugins: ['remove_button'],
                    hideSelected: true,
                    placeholder: "Pilih PIC..."
                });
            }
        }

        function removeRow(btn) {
            const tr = btn.closest('tr');
            const tbody = tr.parentElement;
            tr.remove();

            const remainingTrs = tbody.querySelectorAll('tr:not(.empty-state)');
            remainingTrs.forEach((row, index) => {
                row.querySelector('.number-text').textContent = index + 1;
            });

            if (remainingTrs.length === 0) {
                const emptyRow = document.createElement('tr');
                emptyRow.className = 'empty-state';
                emptyRow.innerHTML = `<td class="px-3 py-8 text-center text-slate-400" colspan="10">Belum ada data. Klik "Tambah Baris" untuk menginput data.</td>`;
                tbody.appendChild(emptyRow);
            }
        }
    </script>
    @endpush
</x-layouts.app>
