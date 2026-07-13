<x-layouts.app title="Governance: {{ $gov->name }}">
    <div class="max-w-5xl mx-auto space-y-6">
        <!-- Header & Breadcrumb -->
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.governance.longlist') }}"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div class="flex-1 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-slate-800 dark:text-white">{{ $gov->name }}</h2>
                    @if($gov->description)
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">{{ $gov->description }}</p>
                    @endif
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('it-work-hub.governance.activities', $gov->id) }}"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg shadow-sm transition-colors">
                        <i class="ti ti-activity"></i> Detail Aktivitas
                    </a>
                </div>
            </div>
        </div>

        @if(session('success'))
            <div class="p-4 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 rounded-lg flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="ti ti-check bg-green-100 dark:bg-green-500/20 p-1 rounded-full text-lg"></i>
                    <p class="font-medium">{{ session('success') }}</p>
                </div>
            </div>
        @endif
        
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Left Column: Info Card -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Project Details Card -->
                <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                    <div class="px-5 py-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                        <h3 class="text-sm font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                            <i class="ti ti-info-circle"></i>
                            Informasi Task
                        </h3>
                        <button onclick="document.getElementById('editModal').classList.remove('hidden')" class="text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 text-sm font-medium flex items-center gap-1">
                            <i class="ti ti-edit"></i> Edit
                        </button>
                    </div>
                    <div class="p-5 grid grid-cols-2 gap-4">
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Priority</p>
                            @if($gov->priority === 'High')
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-red-600">
                                    <i class="ti ti-arrow-up"></i> HIGH
                                </span>
                            @elseif($gov->priority === 'Medium')
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600">
                                    <i class="ti ti-minus"></i> MEDIUM
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-bold text-green-600">
                                    <i class="ti ti-arrow-down"></i> LOW
                                </span>
                            @endif
                        </div>
                        
                        <div>
                            <p class="text-xs text-slate-500 mb-1">Person in Charge (PIC)</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">
                                {{ $gov->pics->count() > 0 ? $gov->pics->pluck('name')->join(', ') : '-' }}
                            </p>
                        </div>
                        
                        <div class="col-span-2">
                            <p class="text-xs text-slate-500 mb-1">Progress Keseluruhan</p>
                            <div class="flex items-center gap-3">
                                <div class="flex-1 h-2 bg-slate-200 dark:bg-slate-700 rounded-full overflow-hidden">
                                    <div class="bg-[#639922] h-full transition-all duration-1000 ease-out" style="width: {{ $gov->progress }}%"></div>
                                </div>
                                <span class="text-sm font-bold text-slate-700 dark:text-slate-300">{{ $gov->progress }}%</span>
                            </div>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 mb-1">Catatan Progress</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">{{ $gov->progress_notes ?: '-' }}</p>
                        </div>

                        <div>
                            <p class="text-xs text-slate-500 mb-1">Tanggal Progress</p>
                            <p class="font-medium text-sm text-slate-800 dark:text-slate-200">{{ $gov->progress_date ? \Carbon\Carbon::parse($gov->progress_date)->format('d M Y') : '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bagian Bawah: Dokumen (Full Width) -->
        <div class="space-y-6">
            <!-- Card Dokumen Pendukung (Tabel) -->
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="p-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex justify-between items-center">
                    <h3 class="text-sm font-semibold text-slate-800 dark:text-white flex items-center gap-2">
                        <i class="ti ti-paperclip"></i> Dokumen Pendukung
                    </h3>
                    <button onclick="addRow('tbody-doc')"
                        class="px-3 py-1.5 bg-[#639922] hover:bg-opacity-90 text-white text-xs font-medium rounded-md transition-colors">
                        + Tambah Dokumen
                    </button>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 dark:text-slate-400">
                        <thead class="bg-slate-200 dark:bg-slate-800 text-xs uppercase font-semibold text-slate-700 dark:text-slate-300 border-b border-slate-300 dark:border-slate-700">
                            <tr>
                                <th class="px-4 py-3">Uraian</th>
                                <th class="px-4 py-3 w-32">Tanggal</th>
                                <th class="px-4 py-3 w-64">File (Upload)</th>
                                <th class="px-4 py-3 w-32 text-center">Link</th>
                                <th class="px-2 py-3 w-16 text-center">Aksi</th>
                            </tr>
                        </thead>
                        <tbody id="tbody-doc" class="divide-y divide-slate-200 dark:divide-slate-800">
                            @forelse($gov->documents as $doc)
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
                                    <button type="button" class="text-white bg-[#639922] hover:bg-opacity-90 rounded px-1.5 py-1 transition-colors mr-1" title="Simpan Baris" onclick="saveRow(this)"><i class="ti ti-device-floppy"></i></button>
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

    <!-- Modal Edit Info -->
    <div id="editModal" class="fixed inset-0 z-[60] hidden bg-slate-900/50 backdrop-blur-sm overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:p-0">
            <div class="relative inline-block w-full max-w-2xl text-left align-middle transition-all transform bg-white dark:bg-slate-900 shadow-xl rounded-2xl overflow-hidden">
                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                    <h3 class="text-lg font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                        <i class="ti ti-edit text-[#639922]"></i>
                        Edit Informasi Task
                    </h3>
                    <button onclick="document.getElementById('editModal').classList.add('hidden')" class="text-slate-400 hover:text-slate-500 bg-slate-100 hover:bg-slate-200 dark:bg-slate-800 dark:hover:bg-slate-700 w-8 h-8 rounded-full flex items-center justify-center transition-colors">
                        <i class="ti ti-x"></i>
                    </button>
                </div>

                <form action="{{ route('it-work-hub.governance.update', $gov->id) }}" method="POST" class="px-6 py-5 space-y-5">
                    @csrf
                    
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Task <span class="text-red-500">*</span></label>
                        <input type="text" name="name" value="{{ $gov->name }}" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian Singkat</label>
                        <textarea name="description" rows="3" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">{{ $gov->description }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Priority <span class="text-red-500">*</span></label>
                            <select name="priority" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                                <option value="Medium" {{ $gov->priority == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="High" {{ $gov->priority == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Low" {{ $gov->priority == 'Low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Catatan Progress</label>
                            <input type="text" name="progress_notes" value="{{ $gov->progress_notes }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>
                        
                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Progress</label>
                            <input type="date" name="progress_date" value="{{ $gov->progress_date ? \Carbon\Carbon::parse($gov->progress_date)->format('Y-m-d') : '' }}" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>
                    </div>

                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300 flex justify-between">
                            <span>Person in Charge (PIC)</span>
                            <span class="text-[10px] text-slate-400 font-normal">Tahan Ctrl/Cmd untuk multiselect</span>
                        </label>
                        <select name="pics[]" multiple class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-[#639922] focus:ring-[#639922] min-h-[120px]">
                            @php $picIds = $gov->pics->pluck('id')->toArray(); @endphp
                            @foreach($users as $u)
                                <option value="{{ $u->id }}" {{ in_array($u->id, $picIds) ? 'selected' : '' }}>{{ $u->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="pt-5 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3 mt-8">
                        <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" class="px-5 py-2 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 dark:bg-slate-800 dark:text-slate-300 dark:border-slate-600 dark:hover:bg-slate-700 transition-colors shadow-sm">
                            Batal
                        </button>
                        <button type="submit" class="px-5 py-2 text-sm font-medium text-white bg-[#639922] border border-transparent rounded-lg hover:bg-[#52821b] transition-colors shadow-sm">
                            Simpan Perubahan
                        </button>
                    </div>
                </form>
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
                        <button type="button" class="text-white bg-[#639922] hover:bg-opacity-90 rounded px-1.5 py-1 transition-colors mr-1" title="Simpan Baris" onclick="saveRow(this)"><i class="ti ti-device-floppy"></i></button>
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
                const response = await fetch("{{ route('it-work-hub.governance.documents.save', $gov->id) }}", {
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
                    const response = await fetch(`/it-work-hub/governance/documents/${documentId}`, {
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
</x-layouts.app>