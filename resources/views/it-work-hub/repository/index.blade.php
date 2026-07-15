<x-layouts.app title="Repository Doc - IT Work Hub">
    <div class="space-y-6">

        {{-- Header --}}
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <a href="{{ route('it-work-hub.dashboard') }}" class="text-sm text-slate-500 hover:text-indigo-600 transition-colors">IT Work Hub</a>
                    <i class="ti ti-chevron-right text-slate-400 text-xs"></i>
                    <span class="text-sm font-medium text-slate-700 dark:text-slate-300">Repository Doc</span>
                </div>
                <h1 class="text-2xl font-bold text-slate-800 dark:text-slate-100 flex items-center gap-2">
                    <i class="ti ti-books text-violet-500"></i> Repository Dokumen
                </h1>
                <p class="text-sm text-slate-500 mt-1">Perpustakaan dokumen IT terorganisir per kategori dan sub-kategori.</p>
            </div>
            <button onclick="openModal('modal-add-type')" class="w-full sm:w-auto inline-flex justify-center items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-all shadow-sm">
                <i class="ti ti-folder-plus"></i> Tambah Jenis
            </button>
        </div>

        {{-- Flash message --}}
        @if(session('success'))
            <div class="p-3 bg-green-50 dark:bg-green-500/10 border border-green-200 dark:border-green-500/20 text-green-700 dark:text-green-400 rounded-lg text-sm mb-2">
                {{ session('success') }}
            </div>
        @endif

        {{-- Search & Filter Bar --}}
        <div class="flex flex-col sm:flex-row items-center justify-end gap-3 w-full">
            <div class="relative w-full sm:w-80">
                <i class="ti ti-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" id="searchInput" placeholder="Cari jenis, sub jenis, dokumen..." class="w-full pl-9 pr-4 py-2 rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm transition-colors">
            </div>
        </div>
        {{-- Accordion Tree --}}
        <div class="space-y-3">
            @forelse($types as $type)
            <div class="type-card bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">

                {{-- Jenis Header --}}
                @php
                    $totalDocs = $type->documents->count() + $type->subTypes->sum(fn($st) => $st->documents->count());
                    $hasContent = $totalDocs > 0 || $type->subTypes->count() > 0;
                    $chevronColor = $hasContent ? 'text-violet-500' : 'text-slate-300 dark:text-slate-600';
                @endphp
                <div class="flex items-center justify-between px-5 py-4 select-none hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors">
                    {{-- Left: expand button + info (clickable) --}}
                    <div class="flex items-center gap-3 flex-1">
                        {{-- Expand chevron (leftmost) --}}
                        <button type="button" id="acc-btn-type-{{ $type->id }}" class="accordion-wrapper flex items-center justify-center w-7 h-7 rounded-lg hover:bg-violet-100 dark:hover:bg-violet-500/20 transition-colors cursor-pointer flex-shrink-0"
                            onclick="toggleAccordion('type-{{ $type->id }}', this)">
                            <i class="ti ti-chevron-right {{ $chevronColor }} text-base accordion-icon transition-transform duration-200"></i>
                        </button>
                        {{-- Folder icon + info --}}
                        <div class="flex items-center gap-3 cursor-pointer flex-1" onclick="document.getElementById('acc-btn-type-{{ $type->id }}').click()">
                            <div class="w-8 h-8 rounded-lg bg-violet-100 dark:bg-violet-500/20 flex items-center justify-center flex-shrink-0">
                                <i class="ti ti-folder text-violet-600 dark:text-violet-400 text-base"></i>
                            </div>
                            <div>
                                <h3 class="searchable-type-name font-semibold text-slate-800 dark:text-slate-100 text-sm">{{ $type->name }}</h3>
                                @if($type->description)
                                    <p class="searchable-type-desc text-xs text-slate-500 dark:text-slate-400">{{ $type->description }}</p>
                                @endif
                            </div>
                            <div class="flex items-center gap-2 ml-1">
                                <span class="text-[10px] px-2 py-0.5 bg-violet-100 dark:bg-violet-500/20 text-violet-700 dark:text-violet-400 rounded-full font-medium">
                                    {{ $totalDocs }} Dok
                                </span>
                                <span class="text-[10px] px-2 py-0.5 bg-slate-100 dark:bg-slate-700 text-slate-600 dark:text-slate-400 rounded-full font-medium">
                                    {{ $type->subTypes->count() }} Sub Jenis
                                </span>
                            </div>
                        </div>
                    </div>

                    {{-- Right: action buttons only --}}
                    <div class="flex items-center gap-1">
                        <button type="button" onclick="openAddSubType({{ $type->id }})"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors text-xs" title="Tambah Sub Jenis">
                            <i class="ti ti-folder-plus"></i>
                        </button>
                        <button type="button" onclick="openAddDocument({{ $type->id }}, null)"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors text-xs" title="Tambah Dokumen Langsung">
                            <i class="ti ti-file-plus"></i>
                        </button>
                        <button type="button" onclick="openEditType({{ $type->id }}, '{{ addslashes($type->name) }}', '{{ addslashes($type->description ?? '') }}')"
                            class="p-1.5 rounded-lg text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors text-xs" title="Edit Jenis">
                            <i class="ti ti-pencil"></i>
                        </button>
                        <form action="{{ route('it-work-hub.repository.types.destroy', $type->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus jenis ini beserta semua sub jenis dan dokumennya?')">
                            @csrf @method('DELETE')
                            <button type="submit"
                                class="p-1.5 rounded-lg text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors text-xs" title="Hapus Jenis">
                                <i class="ti ti-trash"></i>
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Jenis Content (accordion body) --}}
                <div id="type-{{ $type->id }}" class="hidden">

                    {{-- Dokumen Langsung (tanpa sub jenis) --}}
                    @if($type->documents->count() > 0)
                    <div class="px-5 pb-3">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg overflow-hidden">
                            <div class="px-4 py-2 border-b border-slate-200 dark:border-slate-700 flex items-center gap-2">
                                <i class="ti ti-files text-slate-500 text-xs"></i>
                                <span class="text-xs font-semibold text-slate-500 uppercase">Dokumen Umum</span>
                            </div>
                            <table class="w-full text-left text-sm">
                                <thead class="border-b border-slate-200 dark:border-slate-700 text-[10px] uppercase font-semibold text-slate-400">
                                    <tr>
                                        <th class="px-4 py-2">Nama</th>
                                        <th class="px-4 py-2 w-24">Versi</th>
                                        <th class="px-4 py-2 w-32">Tanggal</th>
                                        <th class="px-4 py-2 w-36">Tautan</th>
                                        <th class="px-4 py-2 w-20"></th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                    @foreach($type->documents as $doc)
                                        @include('it-work-hub.repository._document-row', ['doc' => $doc, 'typeId' => $type->id])
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @endif

                    {{-- Sub Jenis --}}
                    @foreach($type->subTypes as $subType)
                    <div class="subtype-card px-5 pb-3">
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg overflow-hidden border border-slate-200 dark:border-slate-700">
                            <div class="flex items-center justify-between px-4 py-2.5 select-none hover:bg-slate-100 dark:hover:bg-slate-700/50 transition-colors">
                                {{-- Left: expand + info --}}
                                <div class="flex items-center gap-2 flex-1">
                                    {{-- Expand chevron (leftmost) --}}
                                    <button type="button" id="acc-btn-subtype-{{ $subType->id }}" class="st-accordion-wrapper flex items-center justify-center w-6 h-6 rounded hover:bg-blue-100 dark:hover:bg-blue-500/20 transition-colors cursor-pointer flex-shrink-0"
                                        onclick="toggleAccordion('subtype-{{ $subType->id }}', this)">
                                        <i class="ti ti-chevron-right {{ $subType->documents->count() > 0 ? 'text-blue-500' : 'text-slate-300 dark:text-slate-600' }} text-sm accordion-icon transition-transform duration-200"></i>
                                    </button>
                                    {{-- Content --}}
                                    <div class="flex items-center gap-2 cursor-pointer flex-1" onclick="document.getElementById('acc-btn-subtype-{{ $subType->id }}').click()">
                                        <i class="ti ti-folder-open text-blue-500 text-sm"></i>
                                        <span class="searchable-subtype-name text-sm font-semibold text-slate-700 dark:text-slate-200">{{ $subType->name }}</span>
                                        @if($subType->description)
                                            <span class="searchable-subtype-desc text-xs text-slate-400">— {{ $subType->description }}</span>
                                        @endif
                                        <span class="text-[10px] px-1.5 py-0.5 bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 rounded-full font-medium">
                                            {{ $subType->documents->count() }} Dok
                                        </span>
                                    </div>
                                </div>
                                {{-- Right: actions only --}}
                                <div class="flex items-center gap-1">
                                    <button type="button" onclick="openAddDocument({{ $type->id }}, {{ $subType->id }})"
                                        class="p-1 rounded text-slate-400 hover:text-emerald-600 hover:bg-emerald-50 dark:hover:bg-emerald-500/10 transition-colors text-xs" title="Tambah Dokumen">
                                        <i class="ti ti-file-plus"></i>
                                    </button>
                                    <button type="button" onclick="openEditSubType({{ $subType->id }}, '{{ addslashes($subType->name) }}', '{{ addslashes($subType->description ?? '') }}')"
                                        class="p-1 rounded text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors text-xs" title="Edit Sub Jenis">
                                        <i class="ti ti-pencil"></i>
                                    </button>
                                    <form action="{{ route('it-work-hub.repository.sub-types.destroy', $subType->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus sub jenis ini beserta semua dokumennya?')">
                                        @csrf @method('DELETE')
                                        <button type="submit"
                                            class="p-1 rounded text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors text-xs" title="Hapus Sub Jenis">
                                            <i class="ti ti-trash"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                            <div id="subtype-{{ $subType->id }}" class="hidden">
                                @if($subType->documents->count() > 0)
                                    <table class="w-full text-left text-sm">
                                        <thead class="border-b border-slate-200 dark:border-slate-700 text-[10px] uppercase font-semibold text-slate-400">
                                            <tr>
                                                <th class="px-4 py-2">Nama</th>
                                                <th class="px-4 py-2 w-24">Versi</th>
                                                <th class="px-4 py-2 w-32">Tanggal</th>
                                                <th class="px-4 py-2 w-36">Tautan</th>
                                                <th class="px-4 py-2 w-20"></th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-200 dark:divide-slate-700">
                                            @foreach($subType->documents as $doc)
                                                @include('it-work-hub.repository._document-row', ['doc' => $doc, 'typeId' => $type->id])
                                            @endforeach
                                        </tbody>
                                    </table>
                                @else
                                    <div class="px-4 py-3 text-xs text-slate-400 italic">Belum ada dokumen.</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @if($type->documents->count() === 0 && $type->subTypes->count() === 0)
                        <div class="px-5 pb-4 text-xs text-slate-400 italic">Belum ada isi. Klik ikon di atas untuk menambahkan Sub Jenis atau Dokumen.</div>
                    @endif
                </div>

            </div>
            @empty
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 p-16 text-center">
                <div class="w-16 h-16 bg-violet-100 dark:bg-violet-500/20 text-violet-500 rounded-full flex items-center justify-center mx-auto mb-4">
                    <i class="ti ti-books text-3xl"></i>
                </div>
                <h3 class="text-lg font-semibold text-slate-700 dark:text-slate-300 mb-2">Repository masih kosong</h3>
                <p class="text-slate-400 text-sm mb-4">Mulai dengan menambahkan Jenis dokumen pertama.</p>
                <button onclick="openModal('modal-add-type')" class="inline-flex items-center gap-2 px-4 py-2 bg-violet-600 hover:bg-violet-700 text-white text-sm font-medium rounded-lg transition-all">
                    <i class="ti ti-folder-plus"></i> Tambah Jenis
                </button>
            </div>
            @endforelse
        </div>

    </div>

    {{-- ── MODAL: Tambah Jenis ── --}}
    <div id="modal-add-type" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-800 dark:text-slate-100">Tambah Jenis</h3>
                <button onclick="closeModal('modal-add-type')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="ti ti-x text-xl"></i></button>
            </div>
            <form action="{{ route('it-work-hub.repository.types.store') }}" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Jenis <span class="text-red-500">*</span></label>
                    <input type="text" name="name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian</label>
                    <textarea name="description" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-add-type')" class="px-4 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: Edit Jenis ── --}}
    <div id="modal-edit-type" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 class="font-bold text-slate-800 dark:text-slate-100">Edit Jenis</h3>
                <button onclick="closeModal('modal-edit-type')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="ti ti-x text-xl"></i></button>
            </div>
            <form id="form-edit-type" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Jenis <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="edit-type-name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian</label>
                    <textarea name="description" id="edit-type-desc" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-violet-500 focus:ring-violet-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-edit-type')" class="px-4 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-violet-600 hover:bg-violet-700 rounded-lg transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: Tambah/Edit Sub Jenis ── --}}
    <div id="modal-sub-type" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-md">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 id="sub-type-modal-title" class="font-bold text-slate-800 dark:text-slate-100">Tambah Sub Jenis</h3>
                <button onclick="closeModal('modal-sub-type')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="ti ti-x text-xl"></i></button>
            </div>
            <form id="form-sub-type" method="POST" class="px-6 py-5 space-y-4">
                @csrf
                <input type="hidden" name="it_wh_repo_type_id" id="sub-type-type-id">
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Sub Jenis <span class="text-red-500">*</span></label>
                    <input type="text" name="name" id="sub-type-name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian</label>
                    <textarea name="description" id="sub-type-desc" rows="2" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm"></textarea>
                </div>
                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-sub-type')" class="px-4 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-lg transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: Tambah/Edit Dokumen ── --}}
    <div id="modal-document" class="fixed inset-0 z-50 hidden bg-slate-900/50 backdrop-blur-sm flex items-center justify-center p-4">
        <div class="bg-white dark:bg-slate-900 rounded-2xl shadow-xl w-full max-w-lg">
            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200 dark:border-slate-800">
                <h3 id="doc-modal-title" class="font-bold text-slate-800 dark:text-slate-100">Tambah Dokumen</h3>
                <button onclick="closeModal('modal-document')" class="text-slate-400 hover:text-slate-600 transition-colors"><i class="ti ti-x text-xl"></i></button>
            </div>
            <form id="form-document" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4" onsubmit="return validateFileSize(this)">
                @csrf
                <input type="hidden" name="it_wh_repo_type_id" id="doc-type-id">
                <input type="hidden" name="it_wh_repo_sub_type_id" id="doc-sub-type-id">

                <div class="grid grid-cols-2 gap-4">
                    <div class="col-span-2 space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Dokumen <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="doc-name" required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Versi</label>
                        <input type="text" name="version" id="doc-version" placeholder="misal: v1.0, Draft" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div class="space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Dokumen</label>
                        <input type="date" name="document_date" id="doc-date" class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div class="col-span-2 space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Link URL</label>
                        <input type="url" name="link" id="doc-link" placeholder="https://..." class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-900 dark:text-slate-100 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm">
                    </div>
                    <div class="col-span-2 space-y-1">
                        <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Upload File <span class="text-xs text-slate-400">(maks. 2 MB)</span></label>
                        <input type="file" name="file" id="doc-file" class="w-full text-sm text-slate-600 dark:text-slate-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-emerald-50 file:text-emerald-700 hover:file:bg-emerald-100 dark:file:bg-emerald-500/20 dark:file:text-emerald-400">
                        <p id="current-file-info" class="text-xs text-slate-400 hidden"></p>
                    </div>
                </div>

                <div class="flex justify-end gap-2 pt-2">
                    <button type="button" onclick="closeModal('modal-document')" class="px-4 py-2 text-sm text-slate-600 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors">Batal</button>
                    <button type="submit" class="px-4 py-2 text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 rounded-lg transition-colors">Simpan</button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── MODAL: Preview Dokumen ── --}}
    <div id="modal-preview" class="fixed inset-0 z-[60] hidden bg-slate-900/80 backdrop-blur-sm">
        <div class="bg-white dark:bg-slate-900 rounded-xl shadow-2xl absolute inset-2 sm:inset-6 lg:inset-10 flex flex-col overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between bg-slate-50 dark:bg-slate-800/80">
                <h3 id="preview-modal-title" class="text-base font-bold text-slate-800 dark:text-white truncate pr-4">Preview Dokumen</h3>
                <div class="flex items-center gap-2">
                    <a id="preview-download-btn" href="#" target="_blank" class="px-3 py-1.5 text-xs font-medium text-emerald-600 bg-emerald-50 hover:bg-emerald-100 dark:text-emerald-400 dark:bg-emerald-500/10 dark:hover:bg-emerald-500/20 rounded-lg transition-colors flex items-center gap-1.5" title="Buka di tab baru / Unduh">
                        <i class="ti ti-external-link"></i> Buka / Unduh
                    </a>
                    <button type="button" onclick="closeModal('modal-preview'); document.getElementById('preview-iframe').src = '';" class="p-1.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 bg-slate-200 hover:bg-slate-300 dark:bg-slate-700 dark:hover:bg-slate-600 rounded-lg transition-colors">
                        <i class="ti ti-x text-sm"></i>
                    </button>
                </div>
            </div>
            <div class="flex-1 bg-slate-100 dark:bg-slate-950/50 p-2 sm:p-4 relative overflow-hidden flex items-center justify-center">
                <div id="preview-loader" class="absolute inset-0 flex items-center justify-center bg-slate-100 dark:bg-slate-950/50 z-10">
                    <i class="ti ti-loader animate-spin text-3xl text-slate-400"></i>
                </div>
                <iframe id="preview-iframe" class="w-full h-full border border-slate-200 dark:border-slate-800 rounded shadow-sm bg-white" onload="document.getElementById('preview-loader').classList.add('hidden')"></iframe>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        function validateFileSize(form) {
            const fileInput = document.getElementById('doc-file');
            if (fileInput.files.length > 0) {
                const fileSize = fileInput.files[0].size / 1024 / 1024; // in MB
                if (fileSize > 2) {
                    alert('Error: Ukuran file terlalu besar. Maksimal 2 MB!');
                    return false; // prevent form submission
                }
            }
            return true;
        }

        function openPreview(url, title) {
            document.getElementById('preview-modal-title').textContent = title || 'Preview Dokumen';
            document.getElementById('preview-download-btn').href = url;
            document.getElementById('preview-loader').classList.remove('hidden');
            document.getElementById('preview-iframe').src = url;
            openModal('modal-preview');
        }

        function openModal(id) {
            document.getElementById(id).classList.remove('hidden');
        }
        function closeModal(id) {
            document.getElementById(id).classList.add('hidden');
        }

        function toggleAccordion(id, trigger) {
            const body = document.getElementById(id);
            const icon = trigger ? trigger.querySelector('.accordion-icon') : null;
            body.classList.toggle('hidden');
            if (icon) icon.classList.toggle('rotate-90');
        }

        function openEditType(id, name, desc) {
            document.getElementById('form-edit-type').action = `/it-work-hub/repository/types/${id}/update`;
            document.getElementById('edit-type-name').value = name;
            document.getElementById('edit-type-desc').value = desc;
            openModal('modal-edit-type');
        }

        function openAddSubType(typeId) {
            document.getElementById('sub-type-modal-title').textContent = 'Tambah Sub Jenis';
            document.getElementById('form-sub-type').action = "{{ route('it-work-hub.repository.sub-types.store') }}";
            document.getElementById('sub-type-type-id').value = typeId;
            document.getElementById('sub-type-name').value = '';
            document.getElementById('sub-type-desc').value = '';
            openModal('modal-sub-type');
        }

        function openEditSubType(id, name, desc) {
            document.getElementById('sub-type-modal-title').textContent = 'Edit Sub Jenis';
            document.getElementById('form-sub-type').action = `/it-work-hub/repository/sub-types/${id}/update`;
            document.getElementById('sub-type-type-id').value = '';
            document.getElementById('sub-type-name').value = name;
            document.getElementById('sub-type-desc').value = desc;
            openModal('modal-sub-type');
        }

        function openAddDocument(typeId, subTypeId) {
            document.getElementById('doc-modal-title').textContent = 'Tambah Dokumen';
            document.getElementById('form-document').action = "{{ route('it-work-hub.repository.documents.store') }}";
            document.getElementById('doc-type-id').value = typeId;
            document.getElementById('doc-sub-type-id').value = subTypeId ?? '';
            document.getElementById('doc-name').value = '';
            document.getElementById('doc-version').value = '';
            document.getElementById('doc-date').value = '';
            document.getElementById('doc-link').value = '';
            document.getElementById('doc-file').value = '';
            document.getElementById('current-file-info').classList.add('hidden');
            openModal('modal-document');
        }

        function openEditDocument(id, name, version, date, link, currentFile) {
            document.getElementById('doc-modal-title').textContent = 'Edit Dokumen';
            document.getElementById('form-document').action = `/it-work-hub/repository/documents/${id}/update`;
            document.getElementById('doc-name').value = name;
            document.getElementById('doc-version').value = version;
            document.getElementById('doc-date').value = date;
            document.getElementById('doc-link').value = link;
            document.getElementById('doc-file').value = '';
            const fileInfo = document.getElementById('current-file-info');
            if (currentFile) {
                fileInfo.textContent = 'File saat ini: ' + currentFile;
                fileInfo.classList.remove('hidden');
            } else {
                fileInfo.classList.add('hidden');
            }
            openModal('modal-document');
        }

        // Close modal on backdrop click
        document.querySelectorAll('[id^="modal-"]').forEach(modal => {
            modal.addEventListener('click', function(e) {
                if (e.target === this) closeModal(this.id);
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('input', function(e) {
            const query = e.target.value.toLowerCase();
            
            document.querySelectorAll('.type-card').forEach(typeCard => {
                let typeMatch = false;
                
                // Check type header
                const typeName = typeCard.querySelector('.searchable-type-name')?.textContent.toLowerCase() || '';
                const typeDesc = typeCard.querySelector('.searchable-type-desc')?.textContent.toLowerCase() || '';
                if (typeName.includes(query) || typeDesc.includes(query)) {
                    typeMatch = true;
                }
                
                // Check subtypes
                let hasVisibleSubType = false;
                typeCard.querySelectorAll('.subtype-card').forEach(subTypeCard => {
                    let subTypeMatch = false;
                    
                    const subTypeName = subTypeCard.querySelector('.searchable-subtype-name')?.textContent.toLowerCase() || '';
                    const subTypeDesc = subTypeCard.querySelector('.searchable-subtype-desc')?.textContent.toLowerCase() || '';
                    if (subTypeName.includes(query) || subTypeDesc.includes(query)) {
                        subTypeMatch = true;
                    }
                    
                    // Check docs within subtype
                    let hasVisibleDoc = false;
                    subTypeCard.querySelectorAll('.doc-row').forEach(docRow => {
                        const docName = docRow.querySelector('.searchable-doc-name')?.textContent.toLowerCase() || '';
                        const docVersion = docRow.querySelector('.searchable-doc-version')?.textContent.toLowerCase() || '';
                        if (docName.includes(query) || docVersion.includes(query) || subTypeMatch || typeMatch) {
                            docRow.style.display = '';
                            hasVisibleDoc = true;
                        } else {
                            docRow.style.display = 'none';
                        }
                    });
                    
                    if (subTypeMatch || hasVisibleDoc || typeMatch) {
                        subTypeCard.style.display = '';
                        hasVisibleSubType = true;
                        // open if filtering and matching child
                        if (query && hasVisibleDoc && !subTypeMatch) {
                            const subTypeBody = subTypeCard.querySelector('div[id^="subtype-"]');
                            if (subTypeBody) subTypeBody.classList.remove('hidden');
                            const subTypeBtn = subTypeCard.querySelector('.st-accordion-wrapper i');
                            if (subTypeBtn && !subTypeBtn.classList.contains('rotate-90')) subTypeBtn.classList.add('rotate-90');
                        }
                    } else {
                        subTypeCard.style.display = 'none';
                    }
                });
                
                // Check direct docs
                let hasVisibleDirectDoc = false;
                typeCard.querySelectorAll('.doc-row').forEach(docRow => {
                    if (!docRow.closest('.subtype-card')) {
                        const docName = docRow.querySelector('.searchable-doc-name')?.textContent.toLowerCase() || '';
                        const docVersion = docRow.querySelector('.searchable-doc-version')?.textContent.toLowerCase() || '';
                        if (docName.includes(query) || docVersion.includes(query) || typeMatch) {
                            docRow.style.display = '';
                            hasVisibleDirectDoc = true;
                        } else {
                            docRow.style.display = 'none';
                        }
                    }
                });
                
                if (typeMatch || hasVisibleSubType || hasVisibleDirectDoc) {
                    typeCard.style.display = '';
                    // open type accordion if searching found something inside
                    if (query && (hasVisibleSubType || hasVisibleDirectDoc)) {
                        const typeBody = typeCard.querySelector('div[id^="type-"]');
                        if (typeBody) typeBody.classList.remove('hidden');
                        const typeBtn = typeCard.querySelector('.accordion-wrapper i');
                        if (typeBtn && !typeBtn.classList.contains('rotate-90')) typeBtn.classList.add('rotate-90');
                    }
                } else {
                    typeCard.style.display = 'none';
                }
            });
        });
    </script>
    @endpush
</x-layouts.app>
