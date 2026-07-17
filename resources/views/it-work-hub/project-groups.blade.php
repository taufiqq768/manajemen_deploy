<x-layouts.app>
    <x-slot name="title">Project Grouping - IT Work Hub</x-slot>

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
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ti ti-layers-linked text-indigo-500"></i> Project Grouping
                    </h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelompokkan beberapa project App Dev ke dalam satu grup besar.</p>
                </div>
                <button onclick="saveAllProjectGroups()" id="btn-save-all" class="px-5 py-2.5 bg-[#639922] hover:bg-[#3B6D11] text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="ti ti-device-floppy"></i> Simpan Semua Perubahan
                </button>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="flex flex-nowrap overflow-x-auto gap-4 pb-2 w-full custom-scrollbar">
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Total Group</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-slate-500 uppercase tracking-wider">Not Started</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $stats['not_started'] }}</p>
            </div>
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-blue-600 uppercase tracking-wider">Development</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-500 mt-1">{{ $stats['progress'] }}</p>
            </div>
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-[#639922] uppercase tracking-wider">Live</p>
                <p class="text-2xl font-bold text-[#639922] dark:text-[#639922] mt-1">{{ $stats['live'] }}</p>
            </div>
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-purple-600 uppercase tracking-wider">Live w/ CR</p>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-500 mt-1">{{ $stats['live_cr'] }}</p>
            </div>
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-amber-600 uppercase tracking-wider">Live (Bug Fix)</p>
                <p class="text-2xl font-bold text-amber-700 dark:text-amber-500 mt-1">{{ $stats['live_bug'] }}</p>
            </div>
            <div class="flex-1 min-w-[140px] bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-[11px] font-bold text-red-600 uppercase tracking-wider">Hold/Retired</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-500 mt-1">{{ $stats['hold'] }}</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            
            <div class="p-4">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-medium text-slate-800 dark:text-slate-200">Daftar Project Group</h3>
                    <button onclick="addRow('tbody-groups')" class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md shadow-sm transition-colors flex items-center gap-1">
                        <i class="ti ti-row-insert-bottom"></i> Tambah Group
                    </button>
                </div>

                <div class="overflow-x-auto pb-4">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-[10px] sm:text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-y border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-2 py-3 w-8 text-center"></th>
                                <th class="px-2 py-3 w-10 text-center">#</th>
                                <th class="px-2 py-3 min-w-[200px]">Nama Project Group</th>
                                <th class="px-2 py-3 w-40 text-center">Status</th>
                                <th class="px-2 py-3 w-64">Aplikasi yang Tergabung</th>
                                <th class="px-2 py-3 w-24 text-center">Progress</th>
                                <th class="px-2 py-3 w-32 text-center">Deadline</th>
                                <th class="px-2 py-3 min-w-[200px]">Keterangan</th>
                                <th class="px-2 py-3 w-10 text-center"></th>
                            </tr>
                        </thead>
                        <tbody id="tbody-groups" class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($projectGroups as $index => $group)
                            <tr data-id="{{ $group->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                                <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span class="number-text">{{ $loop->iteration }}</span></td>
                                <td class="px-1 py-1"><input type="text" class="input-name w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded" value="{{ $group->name }}"></td>
                                <td class="px-1 py-1">
                                    <select onchange="updateGroupStatusColor(this)" class="status-dropdown w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold px-2 py-1.5 rounded uppercase appearance-none cursor-pointer">
                                        @foreach($statuses as $st)
                                            <option value="{{ $st->id }}" style="color: {{ $st->color }}; background-color: #fff;" data-color="{{ $st->color }}" {{ $group->status_id == $st->id ? 'selected' : '' }}>{{ strtoupper($st->name) }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-1 py-1" style="min-width: 250px;">
                                    <select multiple class="input-projects w-full" placeholder="Pilih Aplikasi...">
                                        @foreach($projects as $project)
                                            <option value="{{ $project->id }}" {{ $group->projects->contains('id', $project->id) ? 'selected' : '' }}>{{ $project->name }}</option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-1 py-1 text-center text-xs font-semibold text-slate-600 dark:text-slate-400">
                                    <div class="flex items-center justify-center gap-1">
                                        <div class="w-12 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                            <div class="bg-[#639922] h-full" style="width: {{ $group->progress }}%"></div>
                                        </div>
                                        <span>{{ $group->progress }}%</span>
                                    </div>
                                </td>
                                <td class="px-1 py-1 text-center"><input type="date" class="input-deadline w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded" value="{{ $group->deadline ? \Carbon\Carbon::parse($group->deadline)->format('Y-m-d') : '' }}"></td>
                                <td class="px-1 py-1"><input type="text" class="input-desc w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded" value="{{ $group->description }}"></td>
                                <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
                            </tr>
                            @empty
                            <tr class="empty-state">
                                <td class="px-3 py-8 text-center text-slate-400" colspan="9">Belum ada Group. Klik "Tambah Group" untuk menginput data.</td>
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
            <td class="px-1 py-1"><input type="text" class="input-name w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded" placeholder="Nama Group..."></td>
            <td class="px-1 py-1">
                <select onchange="updateGroupStatusColor(this)" class="status-dropdown w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold px-2 py-1.5 rounded uppercase appearance-none cursor-pointer">
                    @foreach($statuses as $st)
                        <option value="{{ $st->id }}" style="color: {{ $st->color }}; background-color: #fff;" data-color="{{ $st->color }}" {{ $loop->first ? 'selected' : '' }}>{{ strtoupper($st->name) }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-1 py-1" style="min-width: 250px;">
                <select multiple class="input-projects w-full" placeholder="Pilih Aplikasi...">
                    @foreach($projects as $project)
                        <option value="{{ $project->id }}">{{ $project->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-1 py-1 text-center text-xs font-semibold text-slate-400 italic">
                (Auto)
            </td>
            <td class="px-1 py-1 text-center"><input type="date" class="input-deadline w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded"></td>
            <td class="px-1 py-1"><input type="text" class="input-desc w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded" placeholder="Keterangan..."></td>
            <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button" class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
        </tr>
    </template>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
    <script>
        function updateGroupStatusColor(select) {
            const opt = select.options[select.selectedIndex];
            if (opt && opt.dataset.color) {
                select.style.color = opt.dataset.color;
            }
        }

        document.addEventListener('DOMContentLoaded', function() {
            document.querySelectorAll('.status-dropdown').forEach(updateGroupStatusColor);

            const el = document.getElementById('tbody-groups');
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
            document.querySelectorAll('.input-projects').forEach(el => {
                new TomSelect(el, {
                    plugins: ['remove_button'],
                    hideSelected: true,
                    placeholder: "Pilih Aplikasi...",
                    dropdownParent: 'body'
                });
            });
        });

        async function saveAllProjectGroups() {
            const btn = document.getElementById('btn-save-all');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="ti ti-loader animate-spin"></i> Menyimpan...';
            btn.disabled = true;

            const groups = [];
            const tbody = document.getElementById('tbody-groups');
            const rows = tbody.querySelectorAll('tr:not(.empty-state)');
            
            rows.forEach((row, index) => {
                const group = {
                    id: row.dataset.id || null,
                    sort_order: index + 1,
                    name: row.querySelector('.input-name').value,
                    status_id: row.querySelector('.status-dropdown').value,
                    projects: Array.from(row.querySelector('.input-projects').selectedOptions).map(opt => opt.value),
                    deadline: row.querySelector('.input-deadline').value,
                    description: row.querySelector('.input-desc').value
                };
                if (group.name.trim() !== '') {
                    groups.push(group);
                }
            });

            try {
                const response = await fetch("{{ route('it-work-hub.project-groups.save') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ groups })
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
            const newSelect = trElement.querySelector('.input-projects');
            if(newSelect) {
                new TomSelect(newSelect, {
                    plugins: ['remove_button'],
                    hideSelected: true,
                    placeholder: "Pilih Aplikasi...",
                    dropdownParent: 'body'
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
                emptyRow.innerHTML = `<td class="px-3 py-8 text-center text-slate-400" colspan="9">Belum ada Group. Klik "Tambah Group" untuk menginput data.</td>`;
                tbody.appendChild(emptyRow);
            }
        }
    </script>
    @endpush
</x-layouts.app>
