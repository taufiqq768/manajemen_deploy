<x-layouts.app :title="'Dashboard'">

    {{-- Stats grid --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        @php
        $cards = [
            ['label' => 'Total Request', 'status' => '',           'value' => $stats['total'],    'color' => 'indigo',  'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2'],
            ['label' => 'Pending',       'status' => 'pending',    'value' => $stats['pending'],  'color' => 'yellow',  'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Approved',      'status' => 'approved',   'value' => $stats['approved'], 'color' => 'green',   'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
            ['label' => 'Rejected',      'status' => 'rejected',   'value' => $stats['rejected'], 'color' => 'red',     'icon' => 'M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z'],
        ];
        @endphp

        @foreach($cards as $card)
        <a href="{{ route('deploy-requests.index', ['status' => $card['status']]) }}" 
           class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl p-5 flex flex-col gap-3 hover:border-slate-300 dark:hover:border-slate-700 hover:shadow-md transition-all group cursor-pointer block">
            <div class="flex items-center justify-between">
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium uppercase tracking-wider">{{ $card['label'] }}</p>
                <div class="w-8 h-8 rounded-lg
                    @if($card['color'] === 'indigo') bg-indigo-500/20
                    @elseif($card['color'] === 'yellow') bg-yellow-500/20
                    @elseif($card['color'] === 'green') bg-green-500/20
                    @else bg-red-500/20
                    @endif
                    flex items-center justify-center">
                    <svg class="w-4 h-4
                        @if($card['color'] === 'indigo') text-indigo-500
                        @elseif($card['color'] === 'yellow') text-yellow-500
                        @elseif($card['color'] === 'green') text-green-500
                        @else text-red-500
                        @endif"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $card['icon'] }}"/>
                    </svg>
                </div>
            </div>
            <p class="text-3xl font-bold text-slate-900 dark:text-white group-hover:scale-105 origin-left transition-transform">{{ $card['value'] }}</p>
        </a>
        @endforeach
    </div>

    {{-- Recent requests --}}
    <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
        <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200 dark:border-slate-800">
            <h2 class="text-sm font-semibold text-slate-900 dark:text-white">Request Terbaru</h2>
            <a href="{{ route('deploy-requests.index') }}"
               class="text-xs text-indigo-500 dark:text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300 transition-colors">
                Lihat semua →
            </a>
        </div>

        @if($recentRequests->isEmpty())
        <div class="flex flex-col items-center justify-center py-16 text-slate-400 dark:text-slate-500">
            <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                      d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
            </svg>
            <p class="text-sm">Belum ada request deploy</p>
            @if(auth()->user()->isProgrammer())
            <a href="{{ route('deploy-requests.create') }}"
               class="mt-3 text-sm text-indigo-500 dark:text-indigo-400 hover:text-indigo-600 dark:hover:text-indigo-300">
                + Ajukan sekarang
            </a>
            @endif
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
                        <th class="px-5 py-3 text-right"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800">
                    @foreach($recentRequests as $req)
                    @php $badge = $req->statusBadge(); @endphp
                    <tr class="hover:bg-slate-50 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="px-5 py-4">
                            <div class="font-medium text-slate-900 dark:text-white">{{ $req->application->name }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">{{ $req->ticket_number }}</div>
                        </td>
                        <td class="px-5 py-4 text-slate-500 dark:text-slate-300 font-mono text-xs">{{ $req->version }}</td>
                        <td class="px-5 py-4 text-slate-500 dark:text-slate-400 text-xs">{{ $req->jenis }}</td>
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
        @endif
    </div>

</x-layouts.app>
