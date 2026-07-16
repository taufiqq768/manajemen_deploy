<x-layouts.app>
    <x-slot name="title">IT Work Hub - Non App</x-slot>

    <div class="space-y-6">
        {{-- Header Section --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-briefcase text-indigo-500"></i> Non App Longlist
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Kelola dan pantau seluruh project IT Non App.</p>
            </div>
            <div>
                <a href="{{ route('it-work-hub.non-app.create') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-[#639922] hover:bg-[#3B6D11] text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                    <i class="ti ti-plus"></i> Tambah Project
                </a>
            </div>
        </div>

        {{-- Stat Cards --}}
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500 uppercase">Total Project</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $stats['total'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-slate-500 uppercase">Not Started</p>
                <p class="text-2xl font-bold text-slate-800 dark:text-white mt-1">{{ $stats['not_started'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-blue-600 uppercase">Development</p>
                <p class="text-2xl font-bold text-blue-700 dark:text-blue-500 mt-1">{{ $stats['development'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-green-600 uppercase">Live</p>
                <p class="text-2xl font-bold text-green-700 dark:text-green-500 mt-1">{{ $stats['live'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-purple-600 uppercase">Live w/ CR</p>
                <p class="text-2xl font-bold text-purple-700 dark:text-purple-500 mt-1">{{ $stats['live_cr'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-amber-600 uppercase">Live w/ Bug</p>
                <p class="text-2xl font-bold text-amber-700 dark:text-amber-500 mt-1">{{ $stats['live_bug'] }}</p>
            </div>
            <div class="bg-white dark:bg-slate-900 p-4 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 flex flex-col justify-between">
                <p class="text-xs font-medium text-red-600 uppercase">Hold/Retired</p>
                <p class="text-2xl font-bold text-red-700 dark:text-red-500 mt-1">{{ $stats['hold'] }}</p>
            </div>
        </div>

        {{-- Table Card --}}
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <div class="p-4 border-b border-slate-200 dark:border-slate-800 flex justify-between items-center bg-[#F1EFE8] dark:bg-slate-800/50">
                <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-list"></i> Daftar Project Aktif
                </h3>
                <div class="flex gap-2">
                    <div class="relative">
                        <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                        <input type="text" placeholder="Cari project..." class="pl-9 pr-4 py-1.5 text-sm rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 focus:ring-2 focus:ring-[#639922] outline-none transition-shadow w-64">
                    </div>
                    <button class="px-3 py-1.5 bg-white dark:bg-slate-900 border border-slate-300 dark:border-slate-700 rounded-lg text-sm flex items-center gap-2 hover:bg-slate-50 dark:hover:bg-slate-800">
                        <i class="ti ti-filter"></i> Filter
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                    <thead class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                        <tr>
                            <th class="px-2 py-3 w-8 text-center"></th>
                            <th class="px-4 py-3 w-12 text-center">#</th>
                            <th class="px-4 py-3">Nama Project</th>
                            <th class="px-4 py-3 text-center">Status</th>
                            <th class="px-4 py-3 text-center">Prioritas</th>
                            <th class="px-4 py-3 text-center">% Progress</th>
                            <th class="px-4 py-3 text-center">Deadline</th>
                            <th class="px-4 py-3 text-center">Deadline Penyesuaian</th>
                            <th class="px-4 py-3">Squad</th>
                            <th class="px-4 py-3 text-center">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-slate-800">
                        @forelse($projects as $index => $project)
                        <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                            <td class="px-2 py-3 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 cursor-move text-center"><i class="ti ti-grip-vertical text-lg"></i></td>
                            <td class="px-4 py-3 text-center font-medium">{{ $projects->firstItem() + $index }}</td>
                            <td class="px-4 py-3 font-semibold text-slate-800 dark:text-slate-200">{{ $project->name }}</td>
                            <td class="px-4 py-3 text-center">
                                @php
                                    $statusColors = [
                                        'Not Started' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                        'Development' => 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                                        'Live' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                        'Live w/ CR' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                        'Live w/ Bug' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                        'Hold' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                        'Retired' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                                    ];
                                    $color = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                                @endphp
                                <select data-old-value="{{ $project->status }}" onchange="updateProjectStatus({{ $project->id }}, this)" class="px-2 py-1 rounded text-[10px] font-bold w-full {{ $color }} cursor-pointer outline-none appearance-none text-center">
                                    <option value="Not Started" class="bg-white text-slate-800" {{ $project->status == 'Not Started' ? 'selected' : '' }}>NOT STARTED</option>
                                    <option value="Development" class="bg-white text-slate-800" {{ $project->status == 'Development' ? 'selected' : '' }}>DEVELOPMENT</option>
                                    <option value="Live" class="bg-white text-slate-800" {{ $project->status == 'Live' ? 'selected' : '' }}>LIVE</option>
                                    <option value="Live w/ CR" class="bg-white text-slate-800" {{ $project->status == 'Live w/ CR' ? 'selected' : '' }}>LIVE W/ CR</option>
                                    <option value="Live w/ Bug" class="bg-white text-slate-800" {{ $project->status == 'Live w/ Bug' ? 'selected' : '' }}>LIVE W/ BUG</option>
                                    <option value="Hold" class="bg-white text-slate-800" {{ $project->status == 'Hold' ? 'selected' : '' }}>HOLD</option>
                                    <option value="Retired" class="bg-white text-slate-800" {{ $project->status == 'Retired' ? 'selected' : '' }}>RETIRED</option>
                                </select>
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($project->priority === 'High')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400">HIGH</span>
                                @elseif($project->priority === 'Medium')
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400">MEDIUM</span>
                                @else
                                    <span class="px-2 py-1 rounded text-[10px] font-bold bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400">LOW</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-2">
                                    <div class="w-16 h-1.5 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                        <div class="bg-[#639922] h-full" style="width: {{ $project->progress }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold">{{ $project->progress }}%</span>
                                </div>
                            </td>
                            <td class="px-4 py-3 text-center text-xs">{{ $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-3 text-center text-xs text-red-500">{{ $project->adjustment_date ? \Carbon\Carbon::parse($project->adjustment_date)->format('d M Y') : '-' }}</td>
                            <td class="px-4 py-3 text-xs">
                                @if($project->squads->count() > 0)
                                    {{ $project->squads->pluck('name')->join(', ') }}
                                @else
                                    <span class="text-slate-400 italic">Belum ada</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <a href="{{ route('it-work-hub.non-app.show', $project->id) }}" class="inline-flex items-center gap-1 px-2.5 py-1.5 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-medium rounded-md transition-colors" title="Detail">
                                        <i class="ti ti-eye"></i>
                                    </a>
                                    <form action="{{ route('it-work-hub.non-app.destroy', $project->id) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus project ini beserta seluruh aktivitas dan dokumennya?');">
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
                            <td colspan="10" class="px-4 py-8 text-center text-slate-500">Belum ada project yang ditambahkan.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-slate-200 dark:border-slate-800">
                {{ $projects->links() }}
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function updateProjectStatus(id, selectElement) {
            const newStatus = selectElement.value;
            const oldStatus = selectElement.getAttribute('data-old-value');
    
            if (!confirm("Apakah anda akan menyimpan perubahan ini?")) {
                selectElement.value = oldStatus;
                return;
            }
    
            const statusColors = {
                'Not Started': 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                'Development': 'bg-blue-100 text-blue-700 dark:bg-blue-500/20 dark:text-blue-400',
                'Live': 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                'Live w/ CR': 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                'Live w/ Bug': 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                'Hold': 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                'Retired': 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300'
            };
    
            // Update color
            selectElement.className = `px-2 py-1 rounded text-[10px] font-bold w-full ${statusColors[newStatus]} cursor-pointer outline-none appearance-none text-center`;
    
            fetch(`/it-work-hub/non-app/status/${id}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ status: newStatus })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update old value
                    selectElement.setAttribute('data-old-value', newStatus);
                    // Reload halaman untuk update dashboard/stats
                    location.reload();
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal memperbarui status project');
                // Revert back on error
                selectElement.value = oldStatus;
                selectElement.className = `px-2 py-1 rounded text-[10px] font-bold w-full ${statusColors[oldStatus]} cursor-pointer outline-none appearance-none text-center`;
            });
        }
    </script>
    @endpush
</x-layouts.app>
