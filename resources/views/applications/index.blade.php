<x-layouts.app :title="'Manajemen Aplikasi'">
    <link href="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/css/tom-select.css" rel="stylesheet">
    <style>
        .dark .ts-control { background: #1e293b !important; border-color: #334155 !important; color: #e2e8f0 !important; }
        .dark .ts-dropdown { background: #1e293b !important; border-color: #334155 !important; color: #e2e8f0 !important; }
        .dark .ts-dropdown .option:hover, .dark .ts-dropdown .option.active { background: #334155 !important; color: white !important; }
        .dark .ts-control input { color: #e2e8f0 !important; }
        .dark .ts-control .item { background: #4f46e5 !important; border: none !important; color: white !important; }
        .ts-control, .ts-control input, .ts-dropdown { font-size: 0.875rem !important; }
        .ts-control { padding: 0.625rem 0.75rem !important; border-radius: 0.5rem !important; }
    </style>

    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-white">Manajemen Aplikasi</h2>
            <p class="text-sm text-slate-400 mt-0.5">Daftar aplikasi yang dapat di-deploy</p>
        </div>
        <div class="flex items-center gap-3">
            <form method="POST" action="{{ route('applications.sync') }}">
                @csrf
                <button type="submit" 
                   class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-sm font-medium rounded-lg transition-colors border border-slate-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                    </svg>
                    Reload dari GUP
                </button>
            </form>
            <a href="{{ route('applications.create') }}"
               class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                </svg>
                Tambah Aplikasi
            </a>
        </div>
    </div>

    <div class="bg-slate-900 border border-slate-800 rounded-xl overflow-hidden">
        @if($applications->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-slate-500">
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/>
            </svg>
            <p class="text-sm">Belum ada aplikasi terdaftar</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-800/60 text-slate-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Nama Aplikasi</th>
                        <th class="px-5 py-3 text-left">
                            <div class="flex items-center gap-1.5">
                                <span>Versi</span>
                                <button type="button" id="btnRefreshVersions" onclick="ajaxRefreshVersions()" title="Refresh semua versi aplikasi" class="text-slate-400 hover:text-white transition-colors focus:outline-none p-0.5 rounded hover:bg-slate-800">
                                    <svg id="iconRefreshVersions" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                    </svg>
                                </button>
                            </div>
                        </th>
                        <th class="px-5 py-3 text-left">URL Live</th>
                        <th class="px-5 py-3 text-left">Repository</th>
                        <th class="px-5 py-3 text-left">PIC</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($applications as $app)
                    <tr class="hover:bg-slate-800/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="flex items-center gap-2">
                                <p class="font-medium text-white">{{ $app->name }}</p>
                                @if($app->api_id)
                                    <span class="px-1.5 py-0.5 rounded text-[10px] font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20" title="Tersinkronisasi dengan GUP API">GUP</span>
                                @endif
                            </div>
                            @if($app->description)
                            <p class="text-xs text-slate-500 mt-0.5 truncate max-w-xs">{{ $app->description }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <button type="button" 
                               onclick="openVersionApiModal(this)"
                               data-id="{{ $app->id }}"
                               data-name="{{ $app->name }}"
                               data-api-get="{{ $app->version_api_get }}"
                               data-api-write="{{ $app->version_api_write }}"
                               data-api-key="{{ $app->version_api_key }}"
                               data-api-write-key="{{ $app->version_api_write_key }}"
                               data-api-write-notes-key="{{ $app->version_api_write_notes_key }}"
                               data-version="{{ $app->version }}"
                               class="text-indigo-400 hover:text-indigo-300 underline font-medium focus:outline-none"
                               title="Klik untuk atur API Versi">
                                {{ $app->version ?? '—' }}
                            </button>
                        </td>
                        <td class="px-5 py-4">
                            @if($app->app_url)
                            <a href="{{ $app->app_url }}" target="_blank"
                               class="text-emerald-400 hover:text-emerald-300 text-xs transition-colors truncate max-w-xs block">
                                {{ parse_url($app->app_url, PHP_URL_HOST) ?? $app->app_url }}
                            </a>
                            @else
                            <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($app->repo_url)
                            <a href="{{ $app->repo_url }}" target="_blank"
                               class="text-indigo-400 hover:text-indigo-300 text-xs transition-colors truncate max-w-xs block">
                                {{ parse_url($app->repo_url, PHP_URL_HOST) ?? $app->repo_url }}…
                            </a>
                            @else
                            <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            @if($app->pics->isNotEmpty())
                                <div class="flex flex-wrap gap-1">
                                    @foreach($app->pics as $pic)
                                        <span class="px-2 py-0.5 bg-indigo-500/10 text-indigo-400 text-xs rounded border border-indigo-500/20">
                                            {{ $pic->name }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                @if($app->version_api_write)
                                <form method="POST" action="{{ route('applications.push-version', $app) }}"
                                      onsubmit="return confirm('Kirim/push pembaruan versi {{ $app->version }} ke remote server?')">
                                    @csrf
                                    <button type="submit" class="text-emerald-400 hover:text-emerald-300 text-xs transition-colors" title="Kirim paksa versi lokal ke remote server">
                                        Push
                                    </button>
                                </form>
                                @endif
                                <button type="button" 
                                   onclick="openEditModal(this)"
                                   data-id="{{ $app->id }}"
                                   data-name="{{ $app->name }}"
                                   data-url="{{ $app->app_url }}"
                                   data-repo="{{ $app->repo_url }}"
                                   data-version="{{ $app->version }}"
                                   data-desc="{{ $app->description }}"
                                   data-api="{{ $app->api_id ? '1' : '0' }}"
                                   data-pics="{{ $app->pics->pluck('id')->toJson() }}"
                                   class="text-slate-400 hover:text-white text-xs transition-colors">Edit</button>
                                <form method="POST" action="{{ route('applications.destroy', $app) }}"
                                      onsubmit="return confirm('Hapus aplikasi ini? Semua request terkait akan ikut terhapus.')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="text-red-400 hover:text-red-300 text-xs transition-colors">
                                        Hapus
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($applications->hasPages())
        <div class="px-5 py-4 border-t border-slate-800">{{ $applications->links() }}</div>
        @endif
        @endif
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 z-50 hidden bg-black/60 items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl w-full max-w-xl shadow-xl transform scale-95 opacity-0 transition-all duration-200 max-h-[90vh] flex flex-col" id="editModalContent">
            <div class="p-6 sm:p-8 flex-shrink-0 border-b border-slate-800">
                <h2 class="text-lg font-bold text-white">Edit Aplikasi</h2>
            </div>
            <div class="p-6 sm:p-8 overflow-y-auto">
                <form id="editForm" method="POST" action="">
                    @csrf @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Nama Aplikasi <span class="text-red-400">*</span></label>
                            <input type="text" id="edit_name" name="name" required 
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div id="edit_name_text" class="hidden text-slate-300 font-medium py-2 px-3 bg-slate-800/40 rounded-lg border border-slate-800"></div>
                            <input type="hidden" id="edit_name_hidden" name="name">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">URL Live / App</label>
                            <input type="url" id="edit_app_url" name="app_url" 
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <div id="edit_app_url_text" class="hidden text-slate-300 font-medium py-2 px-3 bg-slate-800/40 rounded-lg border border-slate-800"></div>
                            <input type="hidden" id="edit_app_url_hidden" name="app_url">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">URL Repository</label>
                            <input type="url" id="edit_repo_url" name="repo_url" 
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Versi Aplikasi</label>
                            <input type="text" id="edit_version" name="version" placeholder="contoh: 1.0.0"
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Deskripsi</label>
                            <textarea id="edit_description" name="description" rows="3" 
                                      class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500 resize-y"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Pilih PIC (Programmer)</label>
                            <select id="edit_pic_ids" name="pic_ids[]" multiple class="w-full">
                                @foreach($programmers as $programmer)
                                    <option value="{{ $programmer->id }}">{{ $programmer->name }} ({{ $programmer->email }})</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-3 pt-6 mt-6 border-t border-slate-800">
                        <button type="button" onclick="closeEditModal()" class="px-5 py-2.5 text-sm text-slate-400 hover:text-white transition-colors">Batal</button>
                        <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Version API Modal -->
    <div id="versionApiModal" class="fixed inset-0 z-50 hidden bg-black/60 items-center justify-center p-4">
        <div class="bg-slate-900 border border-slate-800 rounded-xl w-full max-w-lg shadow-xl transform scale-95 opacity-0 transition-all duration-200" id="versionApiModalContent">
            <div class="p-6 sm:p-8 flex-shrink-0 border-b border-slate-800 flex justify-between items-center">
                <h2 class="text-lg font-bold text-white">Atur API Versi <span id="version_modal_app_name" class="text-indigo-400 font-medium"></span></h2>
                <div class="text-sm text-slate-400">
                    Versi Saat Ini: <span id="version_modal_current_version" class="text-emerald-400 font-semibold"></span>
                </div>
            </div>
            <div class="p-6 sm:p-8">
                <form id="versionApiForm" method="POST" action="">
                    @csrf @method('PUT')
                    <div class="space-y-5">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">API GET Versi Aplikasi</label>
                            <input type="url" id="version_api_get" name="version_api_get" placeholder="https://api.example.com/version"
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-slate-500 mt-1.5">Endpoint GET untuk mengambil versi aplikasi yang berjalan saat ini.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Key Field Respon API (JSON)</label>
                            <input type="text" id="version_api_key" name="version_api_key" placeholder="contoh: version, atau data.no_versi"
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-slate-500 mt-1.5">Key path JSON respons API (misal <code>version</code>, <code>data.no_versi</code>).</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">API Write / Update Versi</label>
                            <input type="url" id="version_api_write" name="version_api_write" placeholder="https://api.example.com/update-version"
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-slate-500 mt-1.5">Endpoint API untuk menulis / memperbarui status versi aplikasi.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Key Parameter Versi (API Write)</label>
                            <input type="text" id="version_api_write_key" name="version_api_write_key" placeholder="contoh: version, atau no_versi"
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-slate-500 mt-1.5">Key payload JSON untuk data nomor versi (default: <code>version</code>).</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1.5">Key Parameter Catatan Rilis (API Write)</label>
                            <input type="text" id="version_api_write_notes_key" name="version_api_write_notes_key" placeholder="contoh: release_notes, atau keterangan"
                                   class="w-full bg-slate-800 border border-slate-700 text-slate-200 text-sm rounded-lg px-3 py-2.5 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                            <p class="text-xs text-slate-500 mt-1.5">Key payload JSON untuk data catatan rilis/release notes (default: <code>release_notes</code>).</p>
                        </div>
                    </div>
                    <div class="flex items-center justify-between pt-6 mt-6 border-t border-slate-800">
                        <div>
                            <button type="button" id="btnTestVersionApi" onclick="testVersionApi()" 
                                    class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-indigo-400 hover:text-indigo-300 text-xs font-medium rounded-lg transition-colors border border-slate-700 inline-flex items-center gap-1.5 focus:outline-none">
                                <svg id="iconTestVersionApi" class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Tes Get Versi
                            </button>
                        </div>
                        <div class="flex items-center gap-3">
                            <button type="button" onclick="closeVersionApiModal()" class="px-5 py-2.5 text-sm text-slate-400 hover:text-white transition-colors">Batal</button>
                            <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/tom-select@2.2.2/dist/js/tom-select.complete.min.js"></script>
    <script>
        let tsInstance = null;
        let initialApiGet = '';
        let initialApiWrite = '';
        let initialApiKey = '';
        let initialApiWriteKey = '';
        let initialApiWriteNotesKey = '';
        function openEditModal(btn) {
            const modal = document.getElementById('editModal');
            const content = document.getElementById('editModalContent');
            const form = document.getElementById('editForm');
            
            form.action = `/applications/${btn.dataset.id}`;
            
            const isApi = btn.dataset.api === '1';
            
            if (isApi) {
                // Hide input elements, show plain text elements
                document.getElementById('edit_name').classList.add('hidden');
                document.getElementById('edit_name').removeAttribute('name');
                document.getElementById('edit_name').removeAttribute('required');
                
                document.getElementById('edit_name_text').textContent = btn.dataset.name || '';
                document.getElementById('edit_name_text').classList.remove('hidden');
                
                document.getElementById('edit_name_hidden').value = btn.dataset.name || '';
                document.getElementById('edit_name_hidden').disabled = false;

                document.getElementById('edit_app_url').classList.add('hidden');
                document.getElementById('edit_app_url').removeAttribute('name');
                
                document.getElementById('edit_app_url_text').textContent = btn.dataset.url || '—';
                document.getElementById('edit_app_url_text').classList.remove('hidden');
                
                document.getElementById('edit_app_url_hidden').value = btn.dataset.url || '';
                document.getElementById('edit_app_url_hidden').disabled = false;
            } else {
                // Show input elements, hide plain text elements
                document.getElementById('edit_name').classList.remove('hidden');
                document.getElementById('edit_name').setAttribute('name', 'name');
                document.getElementById('edit_name').setAttribute('required', 'required');
                document.getElementById('edit_name').value = btn.dataset.name || '';
                
                document.getElementById('edit_name_text').classList.add('hidden');
                document.getElementById('edit_name_hidden').disabled = true;

                document.getElementById('edit_app_url').classList.remove('hidden');
                document.getElementById('edit_app_url').setAttribute('name', 'app_url');
                document.getElementById('edit_app_url').value = btn.dataset.url || '';
                
                document.getElementById('edit_app_url_text').classList.add('hidden');
                document.getElementById('edit_app_url_hidden').disabled = true;
            }
            
            document.getElementById('edit_repo_url').value = btn.dataset.repo || '';
            document.getElementById('edit_version').value = btn.dataset.version || '';
            document.getElementById('edit_description').value = btn.dataset.desc || '';
            
            if (tsInstance) {
                tsInstance.destroy();
            }
            const selectEl = document.getElementById('edit_pic_ids');
            const pics = JSON.parse(btn.dataset.pics || '[]');
            Array.from(selectEl.options).forEach(opt => {
                opt.selected = pics.includes(parseInt(opt.value));
            });
            
            tsInstance = new TomSelect(selectEl, {
                plugins: ['remove_button'],
                maxItems: null,
            });
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeEditModal() {
            const modal = document.getElementById('editModal');
            const content = document.getElementById('editModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }

        function openVersionApiModal(btn) {
            const modal = document.getElementById('versionApiModal');
            const content = document.getElementById('versionApiModalContent');
            const form = document.getElementById('versionApiForm');
            
            form.action = `/applications/${btn.dataset.id}/version-api`;
            document.getElementById('version_modal_app_name').textContent = `(${btn.dataset.name})`;
            document.getElementById('version_modal_current_version').textContent = btn.dataset.version || '—';
            
            initialApiGet = btn.dataset.apiGet || '';
            initialApiWrite = btn.dataset.apiWrite || '';
            initialApiKey = btn.dataset.apiKey || '';
            initialApiWriteKey = btn.dataset.apiWriteKey || 'version';
            initialApiWriteNotesKey = btn.dataset.apiWriteNotesKey || 'release_notes';

            document.getElementById('version_api_get').value = initialApiGet;
            document.getElementById('version_api_write').value = initialApiWrite;
            document.getElementById('version_api_key').value = initialApiKey;
            document.getElementById('version_api_write_key').value = initialApiWriteKey;
            document.getElementById('version_api_write_notes_key').value = initialApiWriteNotesKey;
            
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            setTimeout(() => {
                content.classList.remove('scale-95', 'opacity-0');
                content.classList.add('scale-100', 'opacity-100');
            }, 10);
        }

        function closeVersionApiModal(force = false) {
            const currentGet = document.getElementById('version_api_get').value.trim();
            const currentWrite = document.getElementById('version_api_write').value.trim();
            const currentKey = document.getElementById('version_api_key').value.trim();
            const currentWriteKey = document.getElementById('version_api_write_key').value.trim();
            const currentWriteNotesKey = document.getElementById('version_api_write_notes_key').value.trim();
            
            if (!force && (currentGet !== initialApiGet || currentWrite !== initialApiWrite || currentKey !== initialApiKey || currentWriteKey !== initialApiWriteKey || currentWriteNotesKey !== initialApiWriteNotesKey)) {
                if (!confirm('Ada perubahan konfigurasi API yang belum disimpan. Yakin ingin membatalkan?')) {
                    return;
                }
            }

            const modal = document.getElementById('versionApiModal');
            const content = document.getElementById('versionApiModalContent');
            content.classList.remove('scale-100', 'opacity-100');
            content.classList.add('scale-95', 'opacity-0');
            setTimeout(() => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            }, 200);
        }

        function ajaxRefreshVersions() {
            const btn = document.getElementById('btnRefreshVersions');
            const icon = document.getElementById('iconRefreshVersions');
            
            if (btn.disabled) return;
            
            btn.disabled = true;
            btn.classList.add('cursor-not-allowed', 'opacity-50');
            icon.classList.add('animate-spin', 'text-indigo-400');
            
            fetch('{{ route('applications.refresh-versions') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    window.location.reload();
                } else {
                    alert('Gagal me-refresh versi: ' + (data.message || 'Terjadi kesalahan'));
                    btn.disabled = false;
                    btn.classList.remove('cursor-not-allowed', 'opacity-50');
                    icon.classList.remove('animate-spin', 'text-indigo-400');
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat me-refresh versi.');
                btn.disabled = false;
                btn.classList.remove('cursor-not-allowed', 'opacity-50');
                icon.classList.remove('animate-spin', 'text-indigo-400');
            });
        }

        function testVersionApi() {
            const urlInput = document.getElementById('version_api_get');
            const keyInput = document.getElementById('version_api_key');
            const btn = document.getElementById('btnTestVersionApi');
            const icon = document.getElementById('iconTestVersionApi');
            
            const url = urlInput.value.trim();
            const key = keyInput.value.trim();
            
            if (!url) {
                alert('Harap isi API GET Versi Aplikasi terlebih dahulu.');
                urlInput.focus();
                return;
            }
            
            if (btn.disabled) return;
            
            btn.disabled = true;
            btn.classList.add('cursor-not-allowed', 'opacity-50');
            icon.classList.add('animate-spin');
            
            fetch('{{ route('applications.test-version-api') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({
                    version_api_get: url,
                    version_api_key: key
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Koneksi Sukses!\nVersi Terbaca: ' + data.version);
                    // Update versi saat ini di modal secara real-time
                    document.getElementById('version_modal_current_version').textContent = data.version;
                } else {
                    alert('Koneksi Gagal!\nDetail: ' + (data.message || 'Respons tidak valid'));
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat menghubungi endpoint uji coba.');
            })
            .finally(() => {
                btn.disabled = false;
                btn.classList.remove('cursor-not-allowed', 'opacity-50');
                icon.classList.remove('animate-spin');
            });
        }

        // Intercept Simpan API Versi menggunakan AJAX
        document.getElementById('versionApiForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const form = this;
            const btn = form.querySelector('button[type="submit"]');
            const originalText = btn.textContent;
            
            btn.disabled = true;
            btn.classList.add('cursor-not-allowed', 'opacity-50');
            btn.textContent = 'Menyimpan...';
            
            const formData = new FormData(form);
            
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                    closeVersionApiModal();
                    window.location.reload();
                } else {
                    alert('Gagal menyimpan: ' + (data.message || 'Terjadi kesalahan'));
                    btn.disabled = false;
                    btn.classList.remove('cursor-not-allowed', 'opacity-50');
                    btn.textContent = originalText;
                }
            })
            .catch(err => {
                console.error(err);
                alert('Terjadi kesalahan koneksi saat menyimpan.');
                btn.disabled = false;
                btn.classList.remove('cursor-not-allowed', 'opacity-50');
                btn.textContent = originalText;
            });
        });
    </script>
    @endpush
</x-layouts.app>
