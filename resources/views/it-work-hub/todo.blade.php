<x-layouts.app>
    <x-slot name="title">IT Work Hub - To-Do List</x-slot>

    @push('scripts')
        <link href="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/css/tom-select.css" rel="stylesheet">
        <style>
            .ts-control {
                border: none;
                background: transparent;
                padding: 4px 6px;
                font-size: 11px;
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

            .dark .ts-dropdown .option:hover,
            .dark .ts-dropdown .option.active {
                background-color: #334155;
                color: white;
            }
        </style>
    @endpush

    @php
        $isAdminOrPM = in_array(auth()->user()->role, ['admin', 'project_manager']);
    @endphp

    <div class="w-full px-4 2xl:px-8 mx-auto space-y-6">
        {{-- Header Section --}}
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-checklist text-[#639922]"></i> To-Do List
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola daftar tugas (To-Do) harian Anda.</p>
            </div>
            <div>
                <button onclick="saveAllTodos()" id="btn-save-all"
                    class="px-5 py-2.5 bg-[#639922] hover:bg-[#3B6D11] text-white font-semibold rounded-lg shadow-sm transition-colors flex items-center gap-2">
                    <i class="ti ti-device-floppy"></i> Simpan Semua Perubahan
                </button>
            </div>
        </div>

        {{-- Table Card --}}
        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div
                class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-[#F1EFE8] dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-list"></i> Daftar Tugas
                </h3>
                <button onclick="addRow('tbody-todos')"
                    class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 dark:bg-slate-700 dark:hover:bg-slate-600 text-white text-xs font-medium rounded-md shadow-sm transition-colors flex items-center gap-1">
                    <i class="ti ti-row-insert-bottom"></i> Tambah Tugas
                </button>
            </div>

            <div class="overflow-x-auto pb-4">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400 whitespace-nowrap">
                    <thead
                        class="bg-slate-200 dark:bg-slate-800 text-[10px] sm:text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                        <tr>
                            <th class="px-2 py-3 w-8 text-center"></th>
                            <th class="px-2 py-3 w-10 text-center">#</th>
                            <!-- <th class="px-2 py-3 w-32 text-center">Tanggal</th> -->
                            <th class="px-2 py-3 w-48">PIC (Nama)</th>
                            <th class="px-2 py-3 min-w-[200px]">Tugas</th>
                            <th class="px-2 py-3 w-32 text-center">Deadline</th>
                            <th class="px-2 py-3 w-40 text-center">Status</th>
                            <th class="px-2 py-3 min-w-[200px]">Catatan</th>
                            <th class="px-2 py-3 w-10 text-center"></th>
                        </tr>
                    </thead>

                    <tbody id="tbody-todos" class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($todos as $index => $todo)
                            <tr data-id="{{ $todo->id }}"
                                class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td
                                    class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center">
                                    <i class="ti ti-grip-vertical text-lg"></i>
                                </td>
                                <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span
                                        class="number-text">{{ $loop->iteration }}</span></td>
                                <!-- <td class="px-2 py-2 text-center text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                                        {{ \Carbon\Carbon::parse($todo->date ?? now())->format('d/m/Y') }}
                                        <input type="hidden" class="input-date" value="{{ \Carbon\Carbon::parse($todo->date ?? now())->format('Y-m-d') }}">
                                    </td> -->
                                <td class="px-1 py-1">
                                    <select class="input-user w-full" placeholder="Pilih PIC..." {{ !$isAdminOrPM ? 'disabled' : '' }}>
                                        @foreach($users as $u)
                                            <option value="{{ $u->id }}" {{ $todo->user_id == $u->id ? 'selected' : '' }}>
                                                {{ $u->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                                <td class="px-1 py-1"><input type="text"
                                        class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded input-task"
                                        value="{{ $todo->task_name }}"></td>
                                <td class="px-1 py-1"><input type="date"
                                        class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded input-deadline"
                                        value="{{ \Carbon\Carbon::parse($todo->deadline)->format('Y-m-d') }}"></td>
                                <td class="px-1 py-1">
                                    <select onchange="updateTodoStatusColor(this)"
                                        class="status-dropdown w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold px-2 py-1.5 rounded uppercase appearance-none cursor-pointer input-status">
                                        <option value="To Do" class="text-slate-500" {{ $todo->status == 'To Do' ? 'selected' : '' }}>TO DO</option>
                                        <option value="In Progress" class="text-blue-500" {{ $todo->status == 'In Progress' ? 'selected' : '' }}>IN PROGRESS</option>
                                        <option value="Done" class="text-[#639922]" {{ $todo->status == 'Done' ? 'selected' : '' }}>DONE</option>
                                    </select>
                                </td>
                                <td class="px-1 py-1"><input type="text"
                                        class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded input-notes"
                                        value="{{ $todo->notes }}" placeholder="Tambahkan catatan..."></td>
                                <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="text-slate-400 hover:text-red-500 transition-colors"
                                        title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button>
                                </td>
                            </tr>
                        @empty
                            <tr class="empty-state">
                                <td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada tugas. Klik "Tambah
                                    Tugas" untuk membuat list baru.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Template for new row -->
    <template id="row-template">
        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
            <td class="px-2 py-1 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center">
                <i class="ti ti-grip-vertical text-lg"></i>
            </td>
            <td class="px-2 py-1 text-center font-medium text-slate-500 row-number"><span class="number-text"></span>
            </td>
            <td class="px-2 py-2 text-center text-xs text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                <span class="display-date">{{ date('d/m/Y') }}</span>
                <input type="hidden" class="input-date" value="{{ date('Y-m-d') }}">
            </td>
            <td class="px-1 py-1">
                <select class="input-user w-full" placeholder="Pilih PIC..." {{ !$isAdminOrPM ? 'disabled' : '' }}>
                    @foreach($users as $u)
                        <option value="{{ $u->id }}" {{ auth()->id() == $u->id ? 'selected' : '' }}>{{ $u->name }}</option>
                    @endforeach
                </select>
            </td>
            <td class="px-1 py-1"><input type="text"
                    class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm font-medium text-slate-800 dark:text-slate-200 px-2 py-1.5 rounded input-task"
                    placeholder="Tugas baru..."></td>
            <td class="px-1 py-1"><input type="date"
                    class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded input-deadline"
                    value="{{ date('Y-m-d') }}"></td>
            <td class="px-1 py-1">
                <select onchange="updateTodoStatusColor(this)"
                    class="status-dropdown w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-[10px] font-bold px-2 py-1.5 rounded uppercase appearance-none cursor-pointer input-status">
                    <option value="To Do" class="text-slate-500" selected>TO DO</option>
                    <option value="In Progress" class="text-blue-500">IN PROGRESS</option>
                    <option value="Done" class="text-[#639922]">DONE</option>
                </select>
            </td>
            <td class="px-1 py-1"><input type="text"
                    class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded input-notes"
                    placeholder="Tambahkan catatan..."></td>
            <td class="px-2 py-1 text-center opacity-0 group-hover:opacity-100 transition-opacity"><button type="button"
                    class="text-slate-400 hover:text-red-500 transition-colors" title="Hapus Baris"
                    onclick="removeRow(this)"><i class="ti ti-trash"></i></button></td>
        </tr>
    </template>

    @push('scripts')
        <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/tom-select@2.3.1/dist/js/tom-select.complete.min.js"></script>
        <script>
            function updateTodoStatusColor(select) {
                select.classList.remove('text-slate-500', 'text-blue-500', 'text-[#639922]');
                switch (select.value) {
                    case 'To Do': select.classList.add('text-slate-500'); break;
                    case 'In Progress': select.classList.add('text-blue-500'); break;
                    case 'Done': select.classList.add('text-[#639922]'); break;
                    default: select.classList.add('text-slate-500'); break;
                }
            }

            function initTomSelect(el) {
                if (el) {
                    new TomSelect(el, {
                        maxItems: 1,
                        hideSelected: true,
                        placeholder: "Pilih PIC..."
                    });
                }
            }

            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.status-dropdown').forEach(updateTodoStatusColor);

                const el = document.getElementById('tbody-todos');
                if (el) {
                    new Sortable(el, {
                        handle: '.cursor-move',
                        animation: 150,
                        onEnd: function () {
                            const rows = el.querySelectorAll('tr:not(.empty-state)');
                            rows.forEach((row, idx) => {
                                row.querySelector('.number-text').textContent = idx + 1;
                            });
                        }
                    });
                }

                // Init Tom Select for existing rows
                document.querySelectorAll('.input-user').forEach(el => {
                    initTomSelect(el);
                });
            });

            async function saveAllTodos() {
                const btn = document.getElementById('btn-save-all');
                const originalText = btn.innerHTML;
                btn.innerHTML = '<i class="ti ti-loader animate-spin"></i> Menyimpan...';
                btn.disabled = true;

                const todos = [];
                const tbody = document.getElementById('tbody-todos');
                const rows = tbody.querySelectorAll('tr:not(.empty-state)');

                rows.forEach((row, index) => {
                    const date = row.querySelector('.input-date').value;
                    const userId = row.querySelector('.input-user').value;
                    const taskName = row.querySelector('.input-task').value;
                    const deadline = row.querySelector('.input-deadline').value;
                    const status = row.querySelector('.input-status').value;
                    const notes = row.querySelector('.input-notes').value;

                    if (taskName.trim() !== '') {
                        todos.push({
                            id: row.dataset.id || null,
                            sort_order: index + 1,
                            date: date,
                            user_id: userId || {{ auth()->id() }},
                            task_name: taskName,
                            deadline: deadline,
                            status: status,
                            notes: notes
                        });
                    }
                });

                try {
                    const response = await fetch("{{ route('it-work-hub.todo.save') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ todos })
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

                const today = new Date();
                const yyyy = today.getFullYear();
                const mm = String(today.getMonth() + 1).padStart(2, '0');
                const dd = String(today.getDate()).padStart(2, '0');

                newRow.querySelector('.input-date').setAttribute('value', `${yyyy}-${mm}-${dd}`);
                const displayDate = newRow.querySelector('.display-date');
                if (displayDate) displayDate.textContent = `${dd}/${mm}/${yyyy}`;

                const trElement = document.createElement('tr');
                trElement.className = newRow.children[0].className;
                trElement.innerHTML = newRow.children[0].innerHTML;

                tbody.appendChild(trElement);

                updateTodoStatusColor(trElement.querySelector('.status-dropdown'));

                // Init TomSelect on the newly appended row
                const newSelect = trElement.querySelector('.input-user');
                initTomSelect(newSelect);
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
                    emptyRow.innerHTML = `<td colspan="9" class="px-4 py-8 text-center text-slate-500">Belum ada tugas. Klik "Tambah Tugas" untuk membuat list baru.</td>`;
                    tbody.appendChild(emptyRow);
                }
            }
        </script>
    @endpush
</x-layouts.app>