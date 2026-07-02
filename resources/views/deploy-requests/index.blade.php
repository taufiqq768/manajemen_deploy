<x-layouts.app :title="'Deploy Requests'">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-xl font-bold text-slate-900 dark:text-white">Deploy Requests</h2>
            <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">
                @if(auth()->user()->isProjectManager() || auth()->user()->isAdmin())
                    Semua request pengajuan deploy masuk
                @else
                    Request deploy yang Anda ajukan
                @endif
            </p>
        </div>
        @if(auth()->user()->isProgrammer())
        <a href="{{ route('deploy-requests.create') }}"
           class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-medium rounded-lg transition-colors">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
            </svg>
            Ajukan Deploy
        </a>
        @endif
    </div>

    {{-- Filter --}}
    <form method="GET" action="{{ route('deploy-requests.index') }}"
          class="flex flex-wrap items-center gap-3 mb-6">
        <select name="status"
                class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Status</option>
            <option value="pending"  {{ request('status') === 'pending'   ? 'selected' : '' }}>Pending</option>
            <option value="approved" {{ request('status') === 'approved'  ? 'selected' : '' }}>Approved</option>
            <option value="rejected" {{ request('status') === 'rejected'  ? 'selected' : '' }}>Rejected</option>
        </select>

        @if(auth()->user()->isProjectManager() || auth()->user()->isAdmin())
        <select name="application_id"
                class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 max-w-[200px]">
            <option value="">Semua Aplikasi</option>
            @foreach($applications as $app)
            <option value="{{ $app->id }}" {{ request('application_id') == $app->id ? 'selected' : '' }}>
                {{ $app->name }}
            </option>
            @endforeach
        </select>
        @endif

        <select name="jenis"
                class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            <option value="">Semua Jenis</option>
            <option value="perubahan_besar" {{ request('jenis') === 'perubahan_besar' ? 'selected' : '' }}>Perubahan Besar</option>
            <option value="perubahan_kecil" {{ request('jenis') === 'perubahan_kecil' ? 'selected' : '' }}>Perubahan Kecil</option>
            <option value="bug_fixing" {{ request('jenis') === 'bug_fixing' ? 'selected' : '' }}>Bug Fixing</option>
        </select>

        <div class="flex items-center gap-2">
            <input type="date" name="start_date" value="{{ request('start_date') }}"
                   class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Dari Tanggal">
            <span class="text-slate-500">-</span>
            <input type="date" name="end_date" value="{{ request('end_date') }}"
                   class="bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-sm rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500" title="Sampai Tanggal">
        </div>

        <button type="submit"
                class="px-4 py-2 bg-slate-700 dark:bg-slate-700 hover:bg-slate-600 text-white text-sm rounded-lg transition-colors">
            Filter
        </button>
        @if(request()->hasAny(['status','application_id','jenis','start_date','end_date']))
        <a href="{{ route('deploy-requests.index') }}"
           class="px-4 py-2 text-slate-500 dark:text-slate-400 hover:text-slate-800 dark:hover:text-white text-sm transition-colors">
            Reset
        </a>
        @endif
    </form>

    {{-- Table --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
        @if($deployRequests->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-slate-400 dark:text-slate-500">
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">Tidak ada request ditemukan</p>
        </div>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 dark:bg-slate-800/60 text-slate-500 dark:text-slate-400 text-xs uppercase tracking-wider">
                    <tr>
                        <th class="px-5 py-3 text-left">Aplikasi</th>
                        <th class="px-5 py-3 text-left">Versi</th>
                        <th class="px-5 py-3 text-left">Jenis</th>
                        @if(auth()->user()->isProjectManager() || auth()->user()->isAdmin())
                        <th class="px-5 py-3 text-left">Pemohon</th>
                        @endif
                        <th class="px-5 py-3 text-left">Tgl. Pengajuan</th>
                        <th class="px-5 py-3 text-left">Rencana Deploy</th>
                        <th class="px-5 py-3 text-left">Status</th>
                        <th class="px-5 py-3 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($deployRequests as $req)
                    @php $badge = $req->statusBadge(); @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $req->application->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $req->ticket_number }}</div>
                        </td>
                        <td class="px-5 py-4 text-slate-500 dark:text-slate-300 font-mono text-xs">
                            <div class="flex items-center gap-1">
                                <span>{{ $req->version }}</span>
                                @if($req->hasFailedVersionUpdate())
                                    <span class="text-red-500 flex-shrink-0" title="Gagal update versi ke remote server (Lihat Log Update Versi)">
                                        <svg class="w-3.5 h-3.5 inline" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                        </svg>
                                    </span>
                                @endif
                            </div>
                        </td>
                        <td class="px-5 py-4">
                            <div class="flex flex-wrap gap-1 max-w-[150px]">
                                @if(is_array($req->jenis))
                                    @foreach($req->jenis as $j)
                                        <span class="bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 px-1.5 py-0.5 rounded text-[10px] font-medium border border-indigo-500/20" title="{{ match($j) {
                                            'perubahan_besar' => 'Perubahan Besar',
                                            'perubahan_kecil' => 'Perubahan Kecil',
                                            'bug_fixing' => 'Bug Fixing',
                                            default => $j
                                        } }}">
                                            {{ match($j) {
                                                'perubahan_besar' => 'Besar',
                                                'perubahan_kecil' => 'Kecil',
                                                'bug_fixing' => 'Bug',
                                                default => $j
                                            } }}
                                        </span>
                                    @endforeach
                                @elseif($req->jenis)
                                    <span class="bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 px-1.5 py-0.5 rounded text-[10px] font-medium border border-indigo-500/20">
                                        {{ $req->jenis === 'CR' ? 'CR' : ($req->jenis === 'Bug' ? 'Bug' : $req->jenis) }}
                                    </span>
                                @else
                                    <span class="text-slate-400">—</span>
                                @endif
                            </div>
                        </td>
                        @if(auth()->user()->isProjectManager() || auth()->user()->isAdmin())
                        <td class="px-5 py-4 text-slate-600 dark:text-slate-400">{{ $req->requester->name }}</td>
                        @endif
                        <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">
                            <span class="block">{{ $req->created_at->addHours(7)->format('d M Y') }}</span>
                            <span class="text-slate-400 dark:text-slate-600">{{ $req->created_at->addHours(7)->format('H:i') }}</span>
                        </td>
                        <td class="px-5 py-4 text-xs">
                            @if($req->scheduled_at)
                                <span class="block text-slate-700 dark:text-slate-300">{{ $req->scheduled_at->format('d M Y') }}</span>
                                <span class="text-slate-400 dark:text-slate-500">{{ $req->scheduled_at->format('H:i') }}</span>
                            @else
                                <span class="text-slate-300 dark:text-slate-600">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-4">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badge['class'] }}">
                                {{ $badge['label'] }}
                            </span>
                            @if($req->isApproved() && $req->approved_at)
                                <span class="block mt-1 text-[10px] text-slate-500 dark:text-slate-400">
                                    {{ $req->approved_at->addHours(7)->format('d/m/Y H:i') }}
                                </span>
                            @endif
                        </td>
                        <td class="px-5 py-4 text-right">
                            <a href="{{ route('deploy-requests.show', $req) }}"
                               class="text-indigo-500 dark:text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300 text-xs transition-colors">
                                Detail →
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($deployRequests->hasPages())
        <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
            {{ $deployRequests->links() }}
        </div>
        @endif
        @endif
    </div>

</x-layouts.app>
