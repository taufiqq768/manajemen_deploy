<x-layouts.app>
    <x-slot name="title">Detail Project Non App - IT Work Hub</x-slot>

    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.non-app.longlist') }}"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $project->name }}</h2>
                        @php
                            $statusColors = [
                                'Not Started' => 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400',
                                'Live' => 'bg-green-100 text-green-700 dark:bg-green-500/20 dark:text-green-400',
                                'Live w/ CR' => 'bg-purple-100 text-purple-700 dark:bg-purple-500/20 dark:text-purple-400',
                                'Live w/ Bug' => 'bg-amber-100 text-amber-700 dark:bg-amber-500/20 dark:text-amber-400',
                                'Hold' => 'bg-red-100 text-red-700 dark:bg-red-500/20 dark:text-red-400',
                                'Retired' => 'bg-slate-200 text-slate-700 dark:bg-slate-700 dark:text-slate-300',
                            ];
                            $color = $statusColors[$project->status] ?? 'bg-slate-100 text-slate-600 dark:bg-slate-800 dark:text-slate-400';
                        @endphp
                        <span
                            class="px-2 py-1 rounded text-[10px] font-bold {{ $color }}">{{ strtoupper($project->status) }}</span>
                    </div>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $project->description ?? '-' }}</p>
                </div>
                <div>
                    <a href="{{ route('it-work-hub.non-app.activities', $project->id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        <i class="ti ti-activity"></i> Detail Aktivitas
                    </a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

            {{-- Kolom Kiri: Info Umum --}}
            <div class="lg:col-span-2 space-y-6">

                {{-- Card Info Umum --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="ti ti-info-circle"></i> Informasi Project
                        </h3>
                        <button onclick="document.getElementById('editProjectModal').classList.remove('hidden')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium flex items-center gap-1">
                            <i class="ti ti-edit"></i> Edit
                        </button>
                    </div>
                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Squad / Tim</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">
                                {{ $project->squads->count() > 0 ? $project->squads->pluck('name')->join(', ') : '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">BPO</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">{{ $project->bpo ?? '-' }}
                            </p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Priority</p>
                            <p class="font-medium text-sm text-red-600">{{ $project->priority }}</p>
                        </div>

                        <div class="col-span-2">
                            <p class="text-xs text-slate-500 mb-1">Progress Keseluruhan</p>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="bg-[#639922] h-full" style="width: {{ $project->progress }}%"></div>
                                </div>
                                <span
                                    class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $project->progress }}%</span>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card Pain Point --}}
                <div
                    class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div
                        class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                            <i class="ti ti-alert-triangle"></i> Pain Point
                        </h3>
                    </div>
                    <div class="p-5 space-y-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Uraian Masalah</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300">
                                {!! nl2br(e($project->pain_point_uraian ?? '-')) !!}</p>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Impact (Dampak)</p>
                            <p class="text-sm text-slate-700 dark:text-slate-300">
                                {!! nl2br(e($project->pain_point_impact ?? '-')) !!}</p>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Tanggal Identifikasi</p>
                                <p class="text-sm text-slate-700 dark:text-slate-300">
                                    {{ $project->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>

        {{-- Bagian Bawah: Dokumen (Full Width) --}}
        <div class="space-y-6">

            {{-- Card Dokumen Pendukung (Tabel) --}}
            <div
                class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div
                    class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ti ti-paperclip"></i> Dokumen Pendukung
                    </h3>
                    <button onclick="addRow('tbody-doc')"
                        class="px-3 py-1.5 bg-[#639922] hover:bg-[#3B6D11] text-white text-xs font-medium rounded-md transition-colors">
                        + Tambah Dokumen
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead
                            class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3">Uraian</th>
                                <th class="px-4 py-3 w-32">Tanggal</th>
                                <th class="px-4 py-3 w-64">File (Upload)</th>
                                <th class="px-4 py-3 w-32 text-center">Link</th>
                                <th class="px-2 py-3 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-doc" class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($project->documents as $doc)
                            <tr data-id="{{ $doc->id }}" class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                                <td class="px-2 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm px-2 py-1.5 rounded" value="{{ $doc->description }}"></td>
                                <td class="px-2 py-1 w-32"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded" value="{{ $doc->document_date ? \Carbon\Carbon::parse($doc->document_date)->format('Y-m-d') : '' }}"></td>
                                <td class="px-2 py-1 w-64">
                                    <div class="flex flex-col gap-1">
                                        @if($doc->file_path)
                                            <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank" class="text-xs text-blue-500 hover:underline truncate w-full" title="Lihat file saat ini"><i class="ti ti-file-text"></i> Lihat File Saat Ini</a>
                                        @endif
                                        <div class="flex items-center gap-2">
                                            <label class="cursor-pointer flex-shrink-0 bg-blue-50 hover:bg-blue-100 text-blue-700 p-1.5 rounded transition-colors" title="Upload File Baru">
                                                <i class="ti ti-upload text-sm"></i>
                                                <input type="file" class="hidden" onchange="this.parentElement.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Belum pilih file'">
                                            </label>
                                            <span class="text-[10px] text-slate-400 truncate w-40" title="Nama file baru">Belum pilih file</span>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-2 py-1 w-32"><input type="url" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded text-center" value="{{ $doc->link }}" placeholder="https://..."></td>
                                <td class="px-2 py-1 w-16 text-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <button type="button" class="text-white bg-[#639922] hover:bg-[#3B6D11] rounded px-1.5 py-1 transition-colors mr-1" title="Simpan Baris" onclick="saveRow(this)"><i class="ti ti-device-floppy"></i></button>
                                    <button type="button" class="text-white bg-red-500 hover:bg-red-600 rounded px-1.5 py-1 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button>
                                </td>
                            </tr>
                            @empty
                            <tr class="empty-state hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="px-4 py-8 text-slate-500 italic text-center" colspan="5">Belum ada dokumen pendukung.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>

    @push('scripts')
    <script>
        function addRow(tbodyId) {
            const tbody = document.getElementById(tbodyId);
            const emptyRow = tbody.querySelector('.empty-state');
            if (emptyRow) emptyRow.remove();

            const template = `
                <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors group">
                    <td class="px-2 py-1"><input type="text" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-sm px-2 py-1.5 rounded" placeholder="Uraian..."></td>
                    <td class="px-2 py-1 w-32"><input type="date" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-1 py-1.5 rounded"></td>
                    <td class="px-2 py-1 w-64">
                        <div class="flex flex-col gap-1">
                            <div class="flex items-center gap-2">
                                <label class="cursor-pointer flex-shrink-0 bg-blue-50 hover:bg-blue-100 text-blue-700 p-1.5 rounded transition-colors" title="Upload File">
                                    <i class="ti ti-upload text-sm"></i>
                                    <input type="file" class="hidden" onchange="this.parentElement.nextElementSibling.textContent = this.files[0] ? this.files[0].name : 'Belum pilih file'">
                                </label>
                                <span class="text-[10px] text-slate-400 truncate w-40" title="Nama file">Belum pilih file</span>
                            </div>
                        </div>
                    </td>
                    <td class="px-2 py-1 w-32"><input type="url" class="w-full bg-transparent border-transparent hover:border-slate-300 focus:border-[#639922] focus:ring-1 focus:ring-[#639922] focus:bg-white text-xs px-2 py-1.5 rounded text-center" placeholder="https://..."></td>
                    <td class="px-2 py-1 w-16 text-center opacity-0 group-hover:opacity-100 transition-opacity">
                        <button type="button" class="text-white bg-[#639922] hover:bg-[#3B6D11] rounded px-1.5 py-1 transition-colors mr-1" title="Simpan Baris" onclick="saveRow(this)"><i class="ti ti-device-floppy"></i></button>
                        <button type="button" class="text-white bg-red-500 hover:bg-red-600 rounded px-1.5 py-1 transition-colors" title="Hapus Baris" onclick="removeRow(this)"><i class="ti ti-trash"></i></button>
                    </td>
                </tr>
            `;
            tbody.insertAdjacentHTML('beforeend', template);
        }

        async function saveRow(btn) {
            const tr = btn.closest('tr');
            const inputs = tr.querySelectorAll('input');
            const documentId = tr.dataset.id || '';
            const formData = new FormData();

            formData.append('_token', '{{ csrf_token() }}');
            if (documentId) formData.append('document_id', documentId);
            formData.append('description', inputs[0].value);
            formData.append('document_date', inputs[1].value);
            if (inputs[2].files.length > 0) {
                formData.append('file', inputs[2].files[0]);
            }
            formData.append('link', inputs[3].value);

            const icon = btn.querySelector('i');
            const originalIcon = icon.className;
            icon.className = 'ti ti-loader animate-spin';

            try {
                const response = await fetch("{{ route('it-work-hub.non-app.documents.save', $project->id) }}", {
                    method: 'POST',
                    body: formData
                });
                
                if (!response.ok) {
                    const errorData = await response.json();
                    let errMsg = 'Gagal menyimpan. ';
                    if (errorData.errors) {
                        errMsg += Object.values(errorData.errors).flat().join(', ');
                    } else if (errorData.message) {
                        errMsg += errorData.message;
                    }
                    throw new Error(errMsg);
                }

                const result = await response.json();
                
                if (result.success) {
                    tr.dataset.id = result.document.id;
                    icon.className = 'ti ti-check text-white';
                    setTimeout(() => icon.className = originalIcon, 2000);
                } else {
                    throw new Error(result.message || 'Gagal menyimpan');
                }
            } catch (error) {
                console.error(error);
                alert(error.message);
                icon.className = 'ti ti-alert-circle text-white';
                setTimeout(() => icon.className = originalIcon, 2000);
            }
        }

        async function removeRow(btn) {
            const tr = btn.closest('tr');
            const documentId = tr.dataset.id;

            if (documentId) {
                if(!confirm('Yakin ingin menghapus dokumen ini?')) return;

                const icon = btn.querySelector('i');
                icon.className = 'ti ti-loader animate-spin';

                try {
                    const response = await fetch(`/it-work-hub/non-app/documents/${documentId}`, {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const result = await response.json();
                    if (!result.success) throw new Error('Gagal menghapus');
                } catch (error) {
                    console.error(error);
                    icon.className = 'ti ti-alert-circle text-red-500';
                    return;
                }
            }
            tr.remove();
        }
    </script>
    @endpush
    {{-- Modal Edit Project --}}
    <div id="editProjectModal" class="fixed inset-0 z-50 hidden">
        <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
        <div class="fixed inset-0 z-10 overflow-y-auto">
            <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
                <div class="relative transform overflow-hidden rounded-xl bg-white dark:bg-slate-900 text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-2xl border border-slate-200 dark:border-slate-800">
                    <form action="{{ route('it-work-hub.non-app.update', $project->id) }}" method="POST">
                        @csrf
                        <div class="px-4 pb-4 pt-5 sm:p-6 sm:pb-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                            <h3 class="text-lg font-semibold leading-6 text-slate-900 dark:text-white flex items-center gap-2">
                                <i class="ti ti-edit"></i> Edit Informasi Project
                            </h3>
                        </div>
                        <div class="px-4 py-5 sm:p-6 space-y-4">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="col-span-2">
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Squad / Tim <span class="font-normal text-slate-400 text-xs">(Gunakan Ctrl/Cmd untuk pilih lebih dari 1)</span></label>
                                    <select name="squads[]" multiple class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922] h-32">
                                        @foreach($users as $user)
                                            <option value="{{ $user->id }}" {{ $project->squads->contains('id', $user->id) ? 'selected' : '' }}>{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">BPO</label>
                                    <input type="text" name="bpo" value="{{ old('bpo', $project->bpo) }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Priority</label>
                                    <select name="priority" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]" required>
                                        <option value="Low" {{ $project->priority == 'Low' ? 'selected' : '' }}>Low</option>
                                        <option value="Medium" {{ $project->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                        <option value="High" {{ $project->priority == 'High' ? 'selected' : '' }}>High</option>
                                    </select>
                                </div>
                                <div class="col-span-2 grid grid-cols-3 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Tanggal Mulai</label>
                                        <input type="date" name="start_date" value="{{ old('start_date', $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Deadline Awal</label>
                                        <input type="date" name="deadline" value="{{ old('deadline', $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Penyesuaian (Opsional)</label>
                                        <input type="date" name="adjustment_date" value="{{ old('adjustment_date', $project->adjustment_date ? \Carbon\Carbon::parse($project->adjustment_date)->format('Y-m-d') : '') }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                    </div>
                                </div>
                                <div class="col-span-2">
                                    <h4 class="text-sm font-semibold text-slate-700 dark:text-slate-300 mt-2 mb-3 pb-1 border-b border-slate-200 dark:border-slate-700">Pain Point</h4>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Uraian Masalah</label>
                                            <textarea name="pain_point_uraian" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">{{ old('pain_point_uraian', $project->pain_point_uraian) }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-slate-700 dark:text-slate-300 mb-1">Impact (Dampak)</label>
                                            <textarea name="pain_point_impact" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">{{ old('pain_point_impact', $project->pain_point_impact) }}</textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 dark:bg-slate-800/50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 border-t border-slate-200 dark:border-slate-800">
                            <button type="submit" class="inline-flex w-full justify-center rounded-lg bg-indigo-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-indigo-500 sm:ml-3 sm:w-auto">Simpan</button>
                            <button type="button" onclick="document.getElementById('editProjectModal').classList.add('hidden')" class="mt-3 inline-flex w-full justify-center rounded-lg bg-white dark:bg-slate-900 px-3 py-2 text-sm font-semibold text-slate-900 dark:text-white shadow-sm ring-1 ring-inset ring-slate-300 dark:ring-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 sm:mt-0 sm:w-auto">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

</x-layouts.app>