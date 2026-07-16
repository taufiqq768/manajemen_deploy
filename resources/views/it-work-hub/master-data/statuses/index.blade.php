<x-layouts.app>
    <x-slot name="title">Master Status</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="ti ti-tags text-indigo-500"></i> Master Status
                </h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data status berdasarkan kategori (Project, Activity, Governance).</p>
            </div>
            <button onclick="openModal('modal-add')" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 bg-[#639922] hover:bg-[#3B6D11] text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                <i class="ti ti-plus"></i> Tambah Status
            </button>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 p-4">
                <form method="GET" action="{{ route('it-work-hub.master-data.statuses.index') }}" class="flex items-center gap-4" id="categoryForm">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Pilih Kategori:</label>
                    <select name="category" onchange="document.getElementById('categoryForm').submit()" class="rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[200px]">
                        <option value="Project App" {{ $category == 'Project App' ? 'selected' : '' }}>Project App Dev</option>
                        <option value="Project Non-App" {{ $category == 'Project Non-App' ? 'selected' : '' }}>Project Non-App</option>
                        <option value="Activity" {{ $category == 'Activity' ? 'selected' : '' }}>Activity</option>
                        <option value="Governance" {{ $category == 'Governance' ? 'selected' : '' }}>Governance</option>
                    </select>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">Urutan</th>
                            <th class="px-6 py-4">Nama Status</th>
                            <th class="px-6 py-4">Kategori</th>
                            <th class="px-6 py-4 text-center">Bobot (%)</th>
                            <th class="px-6 py-4 text-center">Warna (Label)</th>
                            <th class="px-6 py-4 text-center">Status Aktif</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($statuses as $status)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3 text-center font-medium">{{ $status->sort_order }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $status->name }}</td>
                                <td class="px-6 py-3">{{ $status->category }}</td>
                                <td class="px-6 py-3 text-center">{{ $status->weight }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($status->color)
                                        <span class="inline-block w-4 h-4 rounded-full border border-slate-300 dark:border-slate-700 align-middle" style="background-color: {{ $status->color }}"></span>
                                        <span class="ml-2 align-middle text-xs">{{ $status->color }}</span>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-center">
                                    @if($status->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-rose-500/10 text-rose-600 dark:text-rose-400">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="editStatus({{ $status->id }}, '{{ $status->name }}', '{{ $status->category }}', {{ $status->weight }}, '{{ $status->color }}', {{ $status->sort_order }}, {{ $status->is_active ? 'true' : 'false' }})" class="p-1.5 text-slate-400 hover:text-amber-500 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" title="Edit">
                                            <i class="ti ti-edit text-lg"></i>
                                        </button>
                                        <form action="{{ route('it-work-hub.master-data.statuses.destroy', $status->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus status ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-500 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" title="Hapus">
                                                <i class="ti ti-trash text-lg"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                    <i class="ti ti-tag-off text-4xl mb-2 text-slate-300 dark:text-slate-600"></i>
                                    <p>Belum ada data status untuk kategori ini.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Add Status -->
    <div id="modal-add" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-add')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800">
                    <form action="{{ route('it-work-hub.master-data.statuses.store') }}" method="POST" class="p-6">
                        @csrf
                        <div class="mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="ti ti-tags text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100" id="modal-title">Tambah Status</h3>
                                <p class="text-sm text-slate-500">Tambahkan data status baru.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                                <select name="category" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Project App" {{ $category == 'Project App' ? 'selected' : '' }}>Project App Dev</option>
                                    <option value="Project Non-App" {{ $category == 'Project Non-App' ? 'selected' : '' }}>Project Non-App</option>
                                    <option value="Activity" {{ $category == 'Activity' ? 'selected' : '' }}>Activity</option>
                                    <option value="Governance" {{ $category == 'Governance' ? 'selected' : '' }}>Governance</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Status</label>
                                <input type="text" name="name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Misal: Ureq Analysis">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bobot Angka (%)</label>
                                <input type="number" name="weight" value="0" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="0">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Warna Label (Hex/Color Code)</label>
                                <div class="flex gap-2">
                                    <input type="color" name="color_picker" class="h-10 w-12 p-1 rounded border-slate-300 cursor-pointer" oninput="this.nextElementSibling.value = this.value">
                                    <input type="text" name="color" class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="#000000">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Urutan (Sort Order)</label>
                                    <input type="number" name="sort_order" value="1" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="1">
                                </div>
                                <div class="flex items-center pt-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <button type="button" onclick="closeModal('modal-add')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal Edit Status -->
    <div id="modal-edit" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-edit')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-slate-200 dark:border-slate-800">
                    <form id="form-edit" method="POST" class="p-6">
                        @csrf
                        @method('POST')
                        <div class="mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <i class="ti ti-edit text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Edit Status</h3>
                                <p class="text-sm text-slate-500">Ubah data status terpilih.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Kategori</label>
                                <select name="category" id="edit_category" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                    <option value="Project App">Project App Dev</option>
                                    <option value="Project Non-App">Project Non-App</option>
                                    <option value="Activity">Activity</option>
                                    <option value="Governance">Governance</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Status</label>
                                <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Bobot Angka (%)</label>
                                <input type="number" name="weight" id="edit_weight" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Warna Label (Hex/Color Code)</label>
                                <div class="flex gap-2">
                                    <input type="color" id="edit_color_picker" class="h-10 w-12 p-1 rounded border-slate-300 cursor-pointer" oninput="document.getElementById('edit_color').value = this.value">
                                    <input type="text" name="color" id="edit_color" class="flex-1 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Urutan (Sort Order)</label>
                                    <input type="number" name="sort_order" id="edit_sort_order" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                </div>
                                <div class="flex items-center pt-6">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" name="is_active" id="edit_is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                        <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Aktif</span>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 flex justify-end gap-3 pt-4 border-t border-slate-200 dark:border-slate-800">
                            <button type="button" onclick="closeModal('modal-edit')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition-colors">Batal</button>
                            <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition-colors">Simpan Perubahan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function openModal(id) {
            const modal = document.getElementById(id);
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.querySelector('.transform').classList.remove('scale-95', 'opacity-0');
            }, 10);
        }

        function closeModal(id) {
            const modal = document.getElementById(id);
            const content = modal.querySelector('.transform');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.add('hidden');
            }, 200);
        }
        
        function editStatus(id, name, category, weight, color, sortOrder, isActive) {
            document.getElementById('form-edit').action = `/it-work-hub/master-data/statuses/${id}/update`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_category').value = category;
            document.getElementById('edit_weight').value = weight;
            document.getElementById('edit_color').value = color;
            document.getElementById('edit_sort_order').value = sortOrder;
            document.getElementById('edit_is_active').checked = isActive;
            
            if(color && color.startsWith('#')){
                document.getElementById('edit_color_picker').value = color;
            } else {
                document.getElementById('edit_color_picker').value = '#000000';
            }
            
            openModal('modal-edit');
        }
    </script>
    @endpush
</x-layouts.app>
