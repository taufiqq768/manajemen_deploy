<x-layouts.app>
    <x-slot name="title">Master Divisi</x-slot>

    <div class="space-y-6">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
            <div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="ti ti-building text-indigo-500"></i> Master Divisi
                </h1>
                <p class="text-sm text-slate-500 mt-1">Kelola data divisi / unit kerja untuk pelaporan project.</p>
            </div>
            <button onclick="openModal('modal-add')" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 bg-[#639922] hover:bg-[#3B6D11] text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                <i class="ti ti-plus"></i> Tambah Divisi
            </button>
        </div>

        <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 overflow-hidden shadow-sm">
            <div class="border-b border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 p-4">
                <form method="GET" action="{{ route('it-work-hub.master-data.divisions.index') }}" class="flex flex-col sm:flex-row items-start sm:items-center gap-4">
                    <div class="flex-1 w-full sm:w-auto relative">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                            <i class="ti ti-search text-slate-400"></i>
                        </div>
                        <input type="text" name="search" value="{{ $search ?? '' }}" style="padding-left: 2.5rem;" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500 placeholder-slate-400" placeholder="Cari nama atau keterangan divisi...">
                    </div>
                    
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300 whitespace-nowrap">Status:</label>
                        <select name="status" onchange="this.form.submit()" class="rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500 min-w-[140px]">
                            <option value="">Semua Status</option>
                            <option value="1" {{ ($status ?? '') === '1' ? 'selected' : '' }}>Aktif</option>
                            <option value="0" {{ ($status ?? '') === '0' ? 'selected' : '' }}>Tidak Aktif</option>
                        </select>
                        <button type="submit" class="hidden sm:inline-flex justify-center items-center gap-2 px-4 py-2 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-all border border-slate-200 dark:border-slate-700">
                            Cari
                        </button>
                    </div>
                </form>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-50 dark:bg-slate-800/50 text-slate-800 dark:text-slate-200 text-xs uppercase font-semibold">
                        <tr>
                            <th class="px-6 py-4 w-16 text-center">#</th>
                            <th class="px-6 py-4">Nama Divisi</th>
                            <th class="px-6 py-4">Keterangan</th>
                            <th class="px-6 py-4 text-center">Status Aktif</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($divisions as $index => $division)
                            <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-6 py-3 text-center">{{ $divisions->firstItem() + $index }}</td>
                                <td class="px-6 py-3 font-medium text-slate-800 dark:text-slate-200">{{ $division->name }}</td>
                                <td class="px-6 py-3">{{ $division->description ?: '-' }}</td>
                                <td class="px-6 py-3 text-center">
                                    @if($division->is_active)
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-emerald-500/10 text-emerald-600 dark:text-emerald-400">Aktif</span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-2 py-1 rounded-md text-xs font-medium bg-rose-500/10 text-rose-600 dark:text-rose-400">Tidak Aktif</span>
                                    @endif
                                </td>
                                <td class="px-6 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <button onclick="editDivision({{ $division->id }}, '{{ $division->name }}', '{{ $division->description }}', {{ $division->is_active ? 'true' : 'false' }})" class="p-1.5 text-slate-400 hover:text-amber-500 transition-colors rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800" title="Edit">
                                            <i class="ti ti-edit text-lg"></i>
                                        </button>
                                        <form action="{{ route('it-work-hub.master-data.divisions.destroy', $division->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus divisi ini?')">
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
                                <td colspan="5" class="px-6 py-8 text-center text-slate-500 dark:text-slate-400">
                                    <i class="ti ti-building text-4xl mb-2 text-slate-300 dark:text-slate-600"></i>
                                    <p>Belum ada data divisi.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($divisions->hasPages())
                <div class="border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-800/50 p-4">
                    {{ $divisions->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Modal Add Division -->
    <div id="modal-add" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-add')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-800">
                    <form action="{{ route('it-work-hub.master-data.divisions.store') }}" method="POST" class="p-6">
                        @csrf
                        <div class="mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-indigo-500/10 flex items-center justify-center text-indigo-600 dark:text-indigo-400">
                                <i class="ti ti-building text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100" id="modal-title">Tambah Divisi</h3>
                                <p class="text-sm text-slate-500">Tambahkan data divisi baru.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Divisi</label>
                                <input type="text" name="name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Misal: Divisi TI">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan (Opsional)</label>
                                <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Keterangan divisi..."></textarea>
                            </div>
                            <div class="flex items-center pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" checked class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Aktif</span>
                                </label>
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

    <!-- Modal Edit Division -->
    <div id="modal-edit" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" onclick="closeModal('modal-edit')"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-md border border-slate-200 dark:border-slate-800">
                    <form id="form-edit" method="POST" class="p-6">
                        @csrf
                        @method('POST')
                        <div class="mb-5 flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-amber-500/10 flex items-center justify-center text-amber-600 dark:text-amber-400">
                                <i class="ti ti-edit text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg font-semibold text-slate-800 dark:text-slate-100">Edit Divisi</h3>
                                <p class="text-sm text-slate-500">Ubah data divisi terpilih.</p>
                            </div>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Nama Divisi</label>
                                <input type="text" name="name" id="edit_name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Keterangan (Opsional)</label>
                                <textarea name="description" id="edit_description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-indigo-500 focus:ring-indigo-500"></textarea>
                            </div>
                            <div class="flex items-center pt-2">
                                <label class="flex items-center gap-2 cursor-pointer">
                                    <input type="checkbox" name="is_active" id="edit_is_active" class="rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 w-4 h-4">
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Aktif</span>
                                </label>
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
        
        function editDivision(id, name, description, isActive) {
            document.getElementById('form-edit').action = `/it-work-hub/master-data/divisions/${id}/update`;
            document.getElementById('edit_name').value = name;
            document.getElementById('edit_description').value = description;
            document.getElementById('edit_is_active').checked = isActive;
            
            openModal('modal-edit');
        }
    </script>
    @endpush
</x-layouts.app>
