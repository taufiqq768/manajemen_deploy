<x-layouts.app title="IT Work Hub - Governance">
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('it-work-hub.dashboard') }}" class="text-sm text-slate-500 hover:text-[#639922] transition-colors">IT Work Hub</a>
                    <i class="ti ti-chevron-right text-slate-400 text-xs"></i>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Governance</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100">Longlist Governance</h1>
            </div>
            
            <button onclick="document.getElementById('addModal').classList.remove('hidden')" class="inline-flex items-center gap-2 px-4 py-2 bg-[#639922] text-white text-sm font-medium rounded-lg hover:bg-opacity-90 transition-all shadow-sm">
                <i class="ti ti-plus"></i>
                Tambah Task Governance
            </button>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 rounded-lg">
                {{ session('success') }}
            </div>
        @endif

        <!-- List -->
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-200 dark:border-slate-800">
                            <th class="px-2 py-3 w-8 text-center"></th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase">Task Governance</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-24">Priority</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-32">Progress</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-48">Catatan Progress</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-28">Tgl Progress</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase w-32">PIC</th>
                            <th class="px-4 py-3 text-xs font-semibold text-slate-500 uppercase text-center w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800" id="sortable-list">
                        @forelse($governances as $gov)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors" data-id="{{ $gov->id }}">
                            <td class="px-2 py-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                            <td class="px-4 py-3">
                                <div class="font-medium text-slate-800 dark:text-slate-200">{{ $gov->name }}</div>
                                @if($gov->description)
                                    <div class="text-xs text-slate-500 truncate max-w-xs mt-0.5" title="{{ $gov->description }}">{{ $gov->description }}</div>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($gov->priority === 'High')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">HIGH</span>
                                @elseif($gov->priority === 'Medium')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">MEDIUM</span>
                                @else
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">LOW</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="bg-[#639922] h-full" style="width: {{ $gov->progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $gov->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-xs text-slate-600 dark:text-slate-400">
                                {{ $gov->progress_notes ?: '-' }}
                            </td>
                            <td class="px-4 py-3 text-center text-xs">
                                {{ $gov->progress_date ? \Carbon\Carbon::parse($gov->progress_date)->format('d M Y') : '-' }}
                            </td>
                            <td class="px-4 py-3 text-xs">
                                @if($gov->pics->count() > 0)
                                    {{ $gov->pics->pluck('name')->join(', ') }}
                                @else
                                    <span class="text-slate-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('it-work-hub.governance.show', $gov->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-md transition-colors" title="Detail">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <form action="{{ route('it-work-hub.governance.destroy', $gov->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus task governance ini beserta seluruh aktivitas dan dokumennya?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-red-100 hover:bg-red-200 dark:bg-red-500/20 dark:hover:bg-red-500/30 text-red-700 dark:text-red-400 text-xs font-medium rounded-md transition-colors" title="Hapus">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="px-4 py-8 text-center text-slate-500">Belum ada task governance yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Modal Tambah -->
    <div id="addModal" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative inline-block w-full max-w-2xl text-left align-middle transition-all transform bg-white dark:bg-slate-900 shadow-xl rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100">Tambah Task Governance Baru</h3>
                    <button onclick="document.getElementById('addModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500 transition-colors">
                        <i class="ti ti-x text-xl"></i>
                    </button>
                </div>

                <form action="{{ route('it-work-hub.governance.store') }}" method="POST" class="px-6 py-4 space-y-4">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Task <span class="text-red-500">*</span></label>
                        <input type="text" name="name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian Singkat</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]"></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-1 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Priority <span class="text-red-500">*</span></label>
                            <select name="priority" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                                <option value="Medium">Medium</option>
                                <option value="High">High</option>
                                <option value="Low">Low</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Progress</label>
                            <input type="date" name="progress_date" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Catatan Progress</label>
                            <input type="text" name="progress_notes" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">PIC</label>
                        <select name="pics[]" multiple class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922] min-h-[100px]">
                            @foreach($users as $u)
                                <option value="{{ $u->id }}">{{ $u->name }}</option>
                            @endforeach
                        </select>
                        <p class="text-xs text-slate-500 mt-1">Tahan tombol Ctrl (Windows) atau Command (Mac) untuk memilih lebih dari satu.</p>
                    </div>

                    <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
                        <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" class="px-4 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700">
                            Batal
                        </button>
                        <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-[#639922] border border-transparent rounded-lg hover:bg-opacity-90">
                            Simpan Task
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/sortablejs@latest/Sortable.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const list = document.getElementById('sortable-list');
            if(list) {
                new Sortable(list, {
                    handle: '.cursor-move',
                    animation: 150,
                    onEnd: function() {
                        const items = list.querySelectorAll('tr[data-id]');
                        const order = Array.from(items).map(item => item.dataset.id);
                        
                        fetch("{{ route('it-work-hub.governance.update-sort') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({ order: order })
                        }).then(response => response.json())
                          .then(data => {
                              if(!data.success) console.error('Failed to update sort order');
                          });
                    }
                });
            }
        });
    </script>
    @endpush
</x-layouts.app>
