<x-layouts.app :title="'Detail Request Deploy'">

    <div class="max-w-3xl mx-auto">
        {{-- Back --}}
        <a href="{{ route('deploy-requests.index') }}"
            class="inline-flex items-center gap-2 text-sm text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white mb-6 transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
            </svg>
            Kembali
        </a>

        @php $badge = $deployRequest->statusBadge(); @endphp

        {{-- Header card --}}
        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6 mb-4">
            <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-2">
                        <h2 class="text-xl font-bold text-slate-900 dark:text-white flex items-center gap-3">
                            {{ $deployRequest->application->name }}
                            @if($deployRequest->ticket_number)
                                <span class="text-sm font-mono text-slate-500 bg-slate-100 dark:bg-slate-800/50 px-2.5 py-0.5 rounded border border-slate-200 dark:border-slate-700 tracking-tight">{{ $deployRequest->ticket_number }}</span>
                            @endif
                        </h2>
                        <!-- <span
                            class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span> -->
                    </div>
                    <!-- <p class="text-slate-500 dark:text-slate-400 text-sm">
                        <span class="font-mono text-indigo-500 dark:text-indigo-400">{{ $deployRequest->version }}</span>
                        &nbsp;·&nbsp; Environment: <span class="uppercase text-xs font-bold text-orange-500 dark:text-orange-400">{{ $deployRequest->environment }}</span>
                    </p> -->
                </div>

                <div class="flex gap-2 flex-shrink-0">
                    @if(auth()->user()->isProgrammer() && $deployRequest->requester_id === auth()->id() && $deployRequest->isPending())
                        <a href="{{ route('deploy-requests.edit', $deployRequest) }}"
                            class="inline-flex items-center gap-1.5 px-3 py-2 bg-slate-100 dark:bg-slate-700 hover:bg-slate-200 dark:hover:bg-slate-600 text-slate-700 dark:text-white text-sm rounded-lg transition-colors">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg>
                            Revisi
                        </a>
                    @endif

                    @if($deployRequest->isApproved() && $deployRequest->application->version_api_write && !$deployRequest->isVersionSynced())
                        <form method="POST" action="{{ route('deploy-requests.retry-push', $deployRequest) }}" onsubmit="return confirm('Kirim ulang pembaruan versi ke remote server?')">
                            @csrf
                            <button type="submit"
                                class="inline-flex items-center gap-1.5 px-3 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm rounded-lg transition-colors font-medium">
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                                Kirim Ulang Versi
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            {{-- Meta info --}}
            <div
                class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-5 border-t border-slate-200 dark:border-slate-800">
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Diajukan oleh</p>
                    <p class="text-sm text-slate-900 dark:text-white font-medium">{{ $deployRequest->requester->name }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Jenis Request</p>
                    <div class="flex flex-wrap gap-1 mt-1">
                        @if(is_array($deployRequest->jenis))
                            @foreach($deployRequest->jenis as $j)
                                <span class="bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 rounded text-xs border border-indigo-500/20 font-medium">
                                    {{ match($j) {
                                        'perubahan_besar' => 'Perubahan Besar',
                                        'perubahan_kecil' => 'Perubahan Kecil',
                                        'bug_fixing' => 'Bug Fixing',
                                        default => $j
                                    } }}
                                </span>
                            @endforeach
                        @elseif($deployRequest->jenis)
                            <span class="bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 px-2 py-0.5 rounded text-xs border border-indigo-500/20 font-medium">
                                {{ $deployRequest->jenis === 'CR' ? 'Change Request (CR)' : ($deployRequest->jenis === 'Bug' ? 'Bug Fixing' : $deployRequest->jenis) }}
                            </span>
                        @else
                            <span class="text-slate-500">—</span>
                        @endif
                    </div>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Versi</p>
                    <p class="text-sm font-mono text-slate-900 dark:text-white font-semibold">
                        {{ $deployRequest->version }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Tanggal Pengajuan</p>
                    <p class="text-sm text-slate-900 dark:text-white">
                        {{ $deployRequest->created_at->addHours(7)->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Jadwal Deploy</p>
                    <p class="text-sm text-slate-900 dark:text-white">
                        {{ $deployRequest->scheduled_at ? $deployRequest->scheduled_at->format('d M Y, H:i') : '—' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Status</p>
                    <div>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-medium {{ $badge['class'] }}">
                            {{ $badge['label'] }}
                        </span>
                    </div>
                </div>

                @if($deployRequest->isApproved() && $deployRequest->approved_at)
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Disetujui Pada</p>
                    <p class="text-sm text-slate-900 dark:text-white">
                        {{ $deployRequest->approved_at->addHours(7)->format('d M Y, H:i') }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-slate-400 dark:text-slate-500 mb-1">Disetujui Oleh</p>
                    <p class="text-sm text-slate-900 dark:text-white font-medium">
                        {{ $deployRequest->approver ? $deployRequest->approver->name : '—' }}
                    </p>
                </div>
                @endif
            </div>
        </div>

        {{-- Detail sections --}}
        <div class="space-y-4">
            {{-- Release Notes --}}
            <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                    <span class="w-1.5 h-4 bg-indigo-500 rounded-full"></span>
                    Release Notes
                </h3>
                <div class="space-y-4">
                    @if(is_array($deployRequest->release_notes))
                        @if(!empty($deployRequest->release_notes['perubahan_besar']))
                            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 last:border-b-0 last:pb-0">
                                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1 tracking-wider">PERUBAHAN BESAR</div>
                                <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                    {{ $deployRequest->release_notes['perubahan_besar'] }}
                                </p>
                            </div>
                        @endif
                        @if(!empty($deployRequest->release_notes['perubahan_kecil']))
                            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 last:border-b-0 last:pb-0">
                                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1 tracking-wider">PERUBAHAN KECIL</div>
                                <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                    {{ $deployRequest->release_notes['perubahan_kecil'] }}
                                </p>
                            </div>
                        @endif
                        @if(!empty($deployRequest->release_notes['bug_fixing']))
                            <div class="border-b border-slate-100 dark:border-slate-800 pb-3 last:border-b-0 last:pb-0">
                                <div class="text-xs font-bold text-indigo-600 dark:text-indigo-400 mb-1 tracking-wider">BUG FIXING</div>
                                <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                                    {{ $deployRequest->release_notes['bug_fixing'] }}
                                </p>
                            </div>
                        @endif
                    @else
                        <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                            {{ $deployRequest->release_notes }}
                        </p>
                    @endif
                </div>
            </div>

            @if($deployRequest->release_impact)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-yellow-500 rounded-full"></span>
                        Dampak / Impact
                    </h3>
                    <p class="text-sm text-slate-600 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                        {{ $deployRequest->release_impact }}
                    </p>
                </div>
            @endif

            @if($deployRequest->document_support)
                <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-3 flex items-center gap-2">
                        <span class="w-1.5 h-4 bg-sky-500 rounded-full"></span>
                        Dokumen Pendukung
                    </h3>
                    <div class="mt-2">
                        <a href="{{ Storage::url($deployRequest->document_support) }}" target="_blank"
                           class="inline-flex items-center gap-2 px-4 py-2 bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-sm font-medium rounded-lg transition-colors border border-slate-200 dark:border-slate-700">
                            <svg class="w-4 h-4 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            Unduh / Lihat Dokumen
                        </a>
                    </div>
                </div>
            @endif

            @if($deployRequest->isRejected() && $deployRequest->rejection_reason)
                <div class="bg-red-500/5 border border-red-500/30 rounded-xl p-6">
                    <h3 class="text-sm font-semibold text-red-500 dark:text-red-400 mb-3 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Alasan Penolakan
                    </h3>
                    <p class="text-sm text-red-600 dark:text-red-300 whitespace-pre-line leading-relaxed">
                        {{ $deployRequest->rejection_reason }}
                    </p>
                </div>
            @endif
        </div>

        {{-- Approve / Reject form (PM only, jika pending) --}}
        @if((auth()->user()->isProjectManager() || auth()->user()->isAdmin()) && $deployRequest->isPending())
            <div class="mt-6 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-6">
                <h3 class="text-sm font-semibold text-slate-900 dark:text-white mb-5">Keputusan</h3>

                <div class="flex flex-col sm:flex-row gap-3">
                    {{-- Approve --}}
                    <form method="POST" action="{{ route('deploy-requests.approve', $deployRequest) }}" class="flex-1">
                        @csrf
                        <button type="submit" onclick="return confirm('Setujui request deploy ini?')" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-green-600 hover:bg-green-500
                                           text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Approve
                        </button>
                    </form>

                    {{-- Reject --}}
                    <div class="flex-1">
                        <button onclick="document.getElementById('reject-panel').classList.toggle('hidden')" class="w-full flex items-center justify-center gap-2 px-4 py-3 bg-red-600 hover:bg-red-500
                                           text-white text-sm font-semibold rounded-lg transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                            Reject
                        </button>
                    </div>
                </div>

                {{-- Reject form panel --}}
                <div id="reject-panel" class="hidden mt-4">
                    <form method="POST" action="{{ route('deploy-requests.reject', $deployRequest) }}">
                        @csrf
                        <label for="rejection_reason"
                            class="block text-sm font-medium text-slate-600 dark:text-slate-300 mb-2">
                            Alasan Penolakan <span class="text-red-500">*</span>
                        </label>
                        <textarea id="rejection_reason" name="rejection_reason" rows="4" required
                            placeholder="Jelaskan alasan penolakan request ini..."
                            class="w-full bg-slate-50 dark:bg-slate-800 border border-red-500/50 text-slate-800 dark:text-slate-200 text-sm rounded-lg px-3 py-2.5
                                             focus:outline-none focus:ring-2 focus:ring-red-500 resize-none mb-3">{{ old('rejection_reason') }}</textarea>
                        @error('rejection_reason')
                            <p class="mb-3 text-xs text-red-500">{{ $message }}</p>
                        @enderror
                        <button type="submit"
                            class="w-full px-4 py-2.5 bg-red-600 hover:bg-red-500 text-white text-sm font-semibold rounded-lg transition-colors">
                            Konfirmasi Tolak
                        </button>
                    </form>
                </div>
            </div>
        @endif

    </div>

</x-layouts.app>