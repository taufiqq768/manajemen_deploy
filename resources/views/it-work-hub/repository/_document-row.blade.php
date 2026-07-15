<tr class="doc-row hover:bg-slate-100 dark:hover:bg-slate-700/30 transition-colors">
    <td class="px-4 py-2.5 flex items-center gap-2.5">
        <div class="w-7 h-7 rounded bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
            @if($doc->file_path)
                <i class="ti ti-file-filled text-emerald-600 dark:text-emerald-400 text-sm"></i>
            @else
                <i class="ti ti-file-description text-slate-500 text-sm"></i>
            @endif
        </div>
        <span class="searchable-doc-name text-sm font-medium text-slate-800 dark:text-slate-200">{{ $doc->name }}</span>
    </td>
    <td class="px-4 py-2.5">
        @if($doc->version)
            <span class="searchable-doc-version text-[10px] font-semibold px-2 py-0.5 bg-slate-200 dark:bg-slate-700 text-slate-600 dark:text-slate-300 rounded-full">{{ $doc->version }}</span>
        @else
            <span class="text-xs text-slate-400">—</span>
        @endif
    </td>
    <td class="px-4 py-2.5 text-xs text-slate-500 dark:text-slate-400">
        {{ $doc->document_date ? $doc->document_date->format('d M Y') : '—' }}
    </td>
    <td class="px-4 py-2.5">
        <div class="flex items-center gap-2">
            @if($doc->file_path)
                <a href="{{ asset('storage/' . $doc->file_path) }}" target="_blank"
                   class="inline-flex items-center gap-1 text-xs text-emerald-600 dark:text-emerald-400 hover:underline">
                    <i class="ti ti-download text-[11px]"></i> File
                </a>
            @endif
            @if($doc->link)
                <a href="{{ $doc->link }}" target="_blank"
                   class="inline-flex items-center gap-1 text-xs text-blue-600 dark:text-blue-400 hover:underline">
                    <i class="ti ti-external-link text-[11px]"></i> Link
                </a>
            @endif
            @if(!$doc->file_path && !$doc->link)
                <span class="text-xs text-slate-400">—</span>
            @endif
        </div>
    </td>
    <td class="px-4 py-2.5 text-right">
        <div class="flex items-center justify-end gap-1">
            @if($doc->file_path)
            <button type="button"
                onclick="openPreview('{{ asset('storage/' . $doc->file_path) }}', '{{ addslashes($doc->name) }}')"
                class="p-1.5 rounded text-slate-400 hover:text-blue-600 hover:bg-blue-50 dark:hover:bg-blue-500/10 transition-colors text-xs" title="Preview">
                <i class="ti ti-eye"></i>
            </button>
            @endif
            <button type="button"
                onclick="openEditDocument({{ $doc->id }}, '{{ addslashes($doc->name) }}', '{{ addslashes($doc->version ?? '') }}', '{{ $doc->document_date ? $doc->document_date->format('Y-m-d') : '' }}', '{{ addslashes($doc->link ?? '') }}', '{{ $doc->file_path ? basename($doc->file_path) : '' }}')"
                class="p-1.5 rounded text-slate-400 hover:text-amber-600 hover:bg-amber-50 dark:hover:bg-amber-500/10 transition-colors text-xs" title="Edit">
                <i class="ti ti-pencil"></i>
            </button>
            <form action="{{ route('it-work-hub.repository.documents.destroy', $doc->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus dokumen ini?')">
                @csrf @method('DELETE')
                <button type="submit" class="p-1.5 rounded text-slate-400 hover:text-red-600 hover:bg-red-50 dark:hover:bg-red-500/10 transition-colors text-xs" title="Hapus">
                    <i class="ti ti-trash"></i>
                </button>
            </form>
        </div>
    </td>
</tr>
