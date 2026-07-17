<x-layouts.app>
    <x-slot name="title">IT Work Hub - Dashboard</x-slot>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    @endpush

    <div class="w-full mx-auto space-y-6 pb-10">

        {{-- ── Page Header ── --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white flex items-center gap-2">
                    <i class="ti ti-chart-infographic text-indigo-500"></i>
                    Dashboard IT Work Hub
                </h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Helicopter view seluruh aktivitas IT development</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('it-work-hub.longlist') }}" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="ti ti-code"></i> App Dev
                </a>
                <a href="{{ route('it-work-hub.non-app.longlist') }}" class="px-3 py-1.5 bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="ti ti-apps"></i> Non App
                </a>
                <a href="{{ route('it-work-hub.governance.longlist') }}" class="px-3 py-1.5 bg-[#639922] hover:bg-[#52821b] text-white text-xs font-medium rounded-lg shadow-sm transition-colors flex items-center gap-1.5">
                    <i class="ti ti-shield-check"></i> Governance
                </a>
            </div>
        </div>

        {{-- ── KPI Summary Row ── --}}
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            @php
                $kpis = [
                    [
                        'label'    => 'App Development',
                        'projects' => $appDevTotal,
                        'avgProg'  => $appDevAvgProgress,
                        'acts'     => $totalAppDevAct,
                        'overdue'  => $overdueAppDev,
                        'icon'     => 'ti-code',
                        'color'    => 'indigo',
                    ],
                    [
                        'label'    => 'Non App',
                        'projects' => $nonAppTotal,
                        'avgProg'  => $nonAppAvgProgress,
                        'acts'     => $totalNonAppAct,
                        'overdue'  => $overdueNonApp,
                        'icon'     => 'ti-apps',
                        'color'    => 'blue',
                    ],
                    [
                        'label'    => 'Governance',
                        'projects' => $governanceTotal,
                        'avgProg'  => $governanceAvgProgress,
                        'acts'     => $totalGovAct,
                        'overdue'  => $overdueGov,
                        'icon'     => 'ti-shield-check',
                        'color'    => 'emerald',
                    ],
                    [
                        'label'    => 'Project Group',
                        'projects' => $groupTotal,
                        'avgProg'  => $groupAvgProgress,
                        'acts'     => null,
                        'overdue'  => null,
                        'icon'     => 'ti-layers-intersect',
                        'color'    => 'pink',
                    ],
                ];
            @endphp
            @foreach($kpis as $kpi)
            <div class="bg-white dark:bg-slate-900 rounded-xl border border-slate-200 dark:border-slate-800 shadow-sm p-5">
                <div class="flex items-start gap-3 mb-3">
                    <div class="p-2 rounded-lg bg-{{ $kpi['color'] }}-100 dark:bg-{{ $kpi['color'] }}-500/20 flex-shrink-0">
                        <i class="ti {{ $kpi['icon'] }} text-{{ $kpi['color'] }}-600 dark:text-{{ $kpi['color'] }}-400 text-lg"></i>
                    </div>
                    <div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $kpi['label'] }}</p>
                        <p class="text-2xl font-bold text-slate-800 dark:text-white leading-tight">{{ $kpi['projects'] }}</p>
                        <p class="text-[10px] text-slate-400 dark:text-slate-500">Avg progress {{ $kpi['avgProg'] }}%</p>
                    </div>
                </div>
                @if($kpi['acts'] !== null)
                <div class="border-t border-slate-100 dark:border-slate-800 pt-2.5 flex items-center justify-between mt-1">
                    <div class="flex items-center gap-1.5 text-xs text-slate-600 dark:text-slate-400">
                        <i class="ti ti-list-check text-slate-400"></i>
                        <span>Aktivitas: <span class="font-semibold text-slate-700 dark:text-slate-300">{{ $kpi['acts'] }}</span></span>
                    </div>
                    @if($kpi['overdue'] > 0)
                        <a href="{{ route('it-work-hub.overdue-activities') }}" class="text-[10px] font-semibold text-red-500 dark:text-red-400 flex items-center gap-0.5 bg-red-50 dark:bg-red-500/10 px-1.5 py-0.5 rounded-full hover:bg-red-100 dark:hover:bg-red-500/20 transition-colors">
                            <i class="ti ti-alert-circle"></i> {{ $kpi['overdue'] }} Terlambat
                        </a>
                    @else
                        <span class="text-[10px] text-emerald-600 dark:text-emerald-400 flex items-center gap-0.5 bg-emerald-50 dark:bg-emerald-500/10 px-1.5 py-0.5 rounded-full font-medium">
                            <i class="ti ti-circle-check"></i> On Track
                        </span>
                    @endif
                </div>
                @endif
            </div>
            @endforeach
        </div>

        {{-- ── Activity KPI Summary Row (removed — merged into KPI above) ── --}}

        @php
            $priorityDef = [
                'High'   => ['barColor' => '#ef4444', 'text' => 'text-red-700 dark:text-red-400',    'icon' => 'ti-arrow-up'],
                'Medium' => ['barColor' => '#f59e0b', 'text' => 'text-amber-700 dark:text-amber-400','icon' => 'ti-minus'],
                'Low'    => ['barColor' => '#22c55e', 'text' => 'text-green-700 dark:text-green-400','icon' => 'ti-arrow-down'],
            ];
        @endphp

        {{-- ── App Dev Section ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Status Donut --}}
            <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-code text-indigo-500"></i> App Development — Status
                    </h3>
                    <span class="text-xs px-2.5 py-0.5 bg-indigo-100 dark:bg-indigo-500/20 text-indigo-700 dark:text-indigo-400 font-medium rounded-full">{{ $appDevTotal }} Proyek</span>
                </div>
                <div class="p-4">
                    @if($appDevTotal > 0)
                        <div id="chart-app-dev"></div>
                    @else
                        <div class="flex flex-col items-center justify-center h-52 text-slate-400">
                            <i class="ti ti-chart-donut text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Priority Breakdown --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-flag text-amber-500"></i> App Dev — Prioritas
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    @foreach($appDevPriorityStats as $label => $count)
                    @php $def = $priorityDef[$label]; $pct = $appDevTotal > 0 ? round(($count / $appDevTotal) * 100) : 0; @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold {{ $def['text'] }} flex items-center gap-1"><i class="ti {{ $def['icon'] }}"></i> {{ $label }}</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $count }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000" style="width: {{ $pct }}%; background-color: {{ $def['barColor'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
                        <span class="text-slate-500">Rata-rata progress</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-lg">{{ $appDevAvgProgress }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Non App Section ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Status Donut --}}
            <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-apps text-blue-500"></i> Non App Project — Status
                    </h3>
                    <span class="text-xs px-2.5 py-0.5 bg-blue-100 dark:bg-blue-500/20 text-blue-700 dark:text-blue-400 font-medium rounded-full">{{ $nonAppTotal }} Proyek</span>
                </div>
                <div class="p-4">
                    @if($nonAppTotal > 0)
                        <div id="chart-non-app"></div>
                    @else
                        <div class="flex flex-col items-center justify-center h-52 text-slate-400">
                            <i class="ti ti-chart-donut text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Priority Breakdown --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-flag text-amber-500"></i> Non App — Prioritas
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    @foreach($nonAppPriorityStats as $label => $count)
                    @php $def = $priorityDef[$label]; $pct = $nonAppTotal > 0 ? round(($count / $nonAppTotal) * 100) : 0; @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold {{ $def['text'] }} flex items-center gap-1"><i class="ti {{ $def['icon'] }}"></i> {{ $label }}</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $count }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000" style="width: {{ $pct }}%; background-color: {{ $def['barColor'] }}"></div>
                        </div>
                    </div>
                    @endforeach
                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
                        <span class="text-slate-500">Rata-rata progress</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-lg">{{ $nonAppAvgProgress }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Governance Section ── --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Governance Progress Donut --}}
            <div class="md:col-span-2 bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-shield-check text-emerald-600"></i> Governance — Status Progress
                    </h3>
                    <span class="text-xs px-2.5 py-0.5 bg-emerald-100 dark:bg-emerald-500/20 text-emerald-700 dark:text-emerald-400 font-medium rounded-full">{{ $governanceTotal }} Task</span>
                </div>
                <div class="p-4">
                    @if($governanceTotal > 0)
                        <div id="chart-governance"></div>
                    @else
                        <div class="flex flex-col items-center justify-center h-52 text-slate-400">
                            <i class="ti ti-chart-donut text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- Priority Breakdown --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-flag text-amber-500"></i> Governance — Prioritas
                    </h3>
                </div>
                <div class="p-5 space-y-4">
                    @foreach($governancePriorityStats as $label => $count)
                    @php $def = $priorityDef[$label]; $pct = $governanceTotal > 0 ? round(($count / $governanceTotal) * 100) : 0; @endphp
                    <div class="space-y-1.5">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-semibold {{ $def['text'] }} flex items-center gap-1"><i class="ti {{ $def['icon'] }}"></i> {{ $label }}</span>
                            <span class="font-bold text-slate-700 dark:text-slate-300">{{ $count }} <span class="text-slate-400 font-normal">({{ $pct }}%)</span></span>
                        </div>
                        <div class="h-2 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                            <div class="h-full rounded-full transition-all duration-1000" style="width: {{ $pct }}%; background-color: {{ $def['barColor'] }}"></div>
                        </div>
                    </div>
                    @endforeach

                    <div class="pt-3 border-t border-slate-100 dark:border-slate-800 flex justify-between items-center text-xs">
                        <span class="text-slate-500">Rata-rata progress</span>
                        <span class="font-bold text-slate-700 dark:text-slate-200 text-lg">{{ $governanceAvgProgress }}%</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ── Project Group & PIC Section (one row) ── --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

            {{-- Project Group Donut --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-layers-intersect text-pink-500"></i> Project Group
                    </h3>
                    <span class="text-xs px-2.5 py-0.5 bg-pink-100 dark:bg-pink-500/20 text-pink-700 dark:text-pink-400 font-medium rounded-full">{{ $groupTotal }} Group</span>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-center">
                    @if($groupTotal > 0)
                        <div id="chart-project-group"></div>
                    @else
                        <div class="flex flex-col items-center justify-center h-52 text-slate-400">
                            <i class="ti ti-chart-donut text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada data</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- PIC Bar Chart --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50 flex items-center justify-between">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-users text-violet-500"></i> Distribusi Activity per PIC
                    </h3>
                    <span class="text-xs text-slate-400">Semua modul</span>
                </div>
                <div class="p-4 flex-1 flex flex-col justify-center">
                    @if($picSummary->count() > 0)
                        <div id="chart-pic-bar" class="w-full"></div>
                    @else
                        <div class="flex flex-col items-center justify-center h-52 text-slate-400">
                            <i class="ti ti-users text-4xl mb-2"></i>
                            <p class="text-sm">Belum ada data PIC</p>
                        </div>
                    @endif
                </div>
            </div>

            {{-- PIC Leaderboard --}}
            <div class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden flex flex-col">
                <div class="px-5 py-4 border-b border-slate-100 dark:border-slate-800 bg-[#F1EFE8] dark:bg-slate-800/50">
                    <h3 class="font-semibold text-slate-800 dark:text-slate-100 flex items-center gap-2 text-sm">
                        <i class="ti ti-trophy text-amber-500"></i> Beban Kerja PIC
                    </h3>
                </div>
                <div class="flex-1 divide-y divide-slate-100 dark:divide-slate-800 overflow-y-auto">
                    @forelse($picSummary as $index => $pic)
                    @php
                        $prog = $pic['total'] > 0 ? round(($pic['done'] / $pic['total']) * 100) : 0;
                        $medals = ['🥇', '🥈', '🥉'];
                    @endphp
                    <div class="px-5 py-3 flex items-center gap-3">
                        <span class="text-base w-6 text-center flex-shrink-0">{{ $medals[$index] ?? '#' . ($index + 1) }}</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-800 dark:text-slate-200 truncate">{{ $pic['name'] }}</p>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="flex-1 h-1.5 bg-slate-100 dark:bg-slate-800 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full" style="width: {{ $prog }}%; background-color: #7c3aed"></div>
                                </div>
                                <span class="text-[10px] text-slate-500 font-medium flex-shrink-0">{{ $prog }}%</span>
                            </div>
                        </div>
                        <div class="text-right flex-shrink-0">
                            <p class="text-sm font-black text-slate-800 dark:text-slate-100">{{ $pic['total'] }}</p>
                            <p class="text-[10px] text-slate-400">tasks</p>
                        </div>
                    </div>
                    @empty
                    <div class="px-5 py-10 text-center text-slate-400 text-sm">Belum ada data</div>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const isDark = document.documentElement.classList.contains('dark') || localStorage.getItem('theme') !== 'light';
            const textColor = isDark ? '#94a3b8' : '#64748b';
            const gridColor = isDark ? '#1e293b' : '#f1f5f9';
            const bgColor = 'transparent';

            // ── Color Palettes ──────────
            const DB_STATUS_COLORS = @json($dbStatusColors ?? []);
            const STATUS_COLORS = {
                // Shared Statuses
                'Not Started':  '#94a3b8', // slate-400
                'Development':  '#3b82f6', // blue-500
                'Progress':     '#3b82f6', // blue-500
                'On Progress':  '#3b82f6', // blue-500
                'Done':         '#22c55e', // green-500
                'Hold':         '#ef4444', // red-500
                'Retired':      '#64748b', // slate-500
                
                // Specific App Dev / Group
                'Live':         '#22c55e', // green-500
                'Live w/ CR':   '#a855f7', // purple-500
                'Live w/ Bug':  '#f59e0b', // amber-500
                'Live (Bug Fixing)': '#f59e0b', // amber-500
                
                ...DB_STATUS_COLORS
            };

            function getColors(labels) {
                return labels.map(l => STATUS_COLORS[l] || '#94a3b8');
            }

            const donutBase = {
                chart: { type: 'donut', height: 280, background: bgColor, fontFamily: 'inherit', animations: { speed: 800 } },
                plotOptions: {
                    pie: {
                        donut: {
                            size: '60%',
                            labels: {
                                show: true,
                                name: { show: true, color: textColor, fontSize: '12px' },
                                value: { show: true, color: textColor, fontSize: '22px', fontWeight: 700, formatter: v => v },
                                total: { show: true, label: 'Total', color: textColor, fontSize: '11px',
                                    formatter: w => w.globals.seriesTotals.reduce((a, b) => a + b, 0) }
                            }
                        }
                    }
                },
                dataLabels: { enabled: true, formatter: (val, opts) => opts.w.globals.series[opts.seriesIndex], dropShadow: { enabled: false } },
                stroke: { width: 2, colors: [isDark ? '#0f172a' : '#ffffff'] },
                legend: { position: 'bottom', labels: { colors: textColor }, markers: { width: 10, height: 10, radius: 3 } },
                theme: { mode: isDark ? 'dark' : 'light' },
                tooltip: { theme: isDark ? 'dark' : 'light' },
            };

            // ── App Dev Chart ──────────────────────────────────────────
            @if($appDevTotal > 0)
            const appDevData = @json($appDevStats->filter(fn($v) => $v > 0));
            const appDevLabels = Object.keys(appDevData);
            const appDevSeries = Object.values(appDevData);
            new ApexCharts(document.querySelector("#chart-app-dev"), {
                ...donutBase, series: appDevSeries, labels: appDevLabels, colors: getColors(appDevLabels),
            }).render();
            @endif

            // ── Non App Chart ──────────────────────────────────────────
            @if($nonAppTotal > 0)
            const nonAppData = @json($nonAppStats->filter(fn($v) => $v > 0));
            const nonAppLabels = Object.keys(nonAppData);
            const nonAppSeries = Object.values(nonAppData);
            new ApexCharts(document.querySelector("#chart-non-app"), {
                ...donutBase, series: nonAppSeries, labels: nonAppLabels, colors: getColors(nonAppLabels),
            }).render();
            @endif

            // ── Governance Chart ───────────────────────────────────────
            @if($governanceTotal > 0)
            const govData = @json(array_filter($governanceStats, fn($v) => $v > 0));
            const govLabels = Object.keys(govData);
            const govSeries = Object.values(govData);
            new ApexCharts(document.querySelector("#chart-governance"), {
                ...donutBase, series: govSeries, labels: govLabels, colors: getColors(govLabels),
            }).render();
            @endif

            // ── Project Group Chart ────────────────────────────────────
            @if($groupTotal > 0)
            const groupData = @json($groupStats->filter(fn($v) => $v > 0));
            const groupLabels = Object.keys(groupData);
            const groupSeries = Object.values(groupData);
            new ApexCharts(document.querySelector("#chart-project-group"), {
                ...donutBase, series: groupSeries, labels: groupLabels, colors: getColors(groupLabels),
            }).render();
            @endif

            // ── PIC Bar Chart ──────────────────────────────────────────
            @if($picSummary->count() > 0)
            new ApexCharts(document.querySelector("#chart-pic-bar"), {
                chart: {
                    type: 'bar', height: 280, background: bgColor, fontFamily: 'inherit',
                    toolbar: { show: false },
                    animations: { speed: 800 }
                },
                series: [
                    { name: 'Total Activity', data: @json($picTotals) },
                    { name: 'Selesai (Done)', data: @json($picDone) },
                ],
                xaxis: {
                    categories: @json($picNames),
                    labels: { style: { colors: textColor, fontSize: '10px' }, trim: true, hideOverlappingLabels: false },
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                },
                yaxis: {
                    labels: { style: { colors: textColor } },
                },
                colors: ['#a5b4fc', '#22c55e'],
                plotOptions: {
                    bar: { borderRadius: 3, columnWidth: '60%', dataLabels: { position: 'top' } }
                },
                dataLabels: { enabled: true, style: { fontSize: '10px', colors: [textColor] }, offsetY: -20 },
                grid: { borderColor: gridColor, strokeDashArray: 4, xaxis: { lines: { show: false } }, padding: { top: 0, right: 0, bottom: 0, left: 10 } },
                legend: { position: 'top', horizontalAlign: 'right', labels: { colors: textColor }, markers: { width: 10, height: 10, radius: 2 } },
                theme: { mode: isDark ? 'dark' : 'light' },
                tooltip: { theme: isDark ? 'dark' : 'light' },
            }).render();
            @endif

            // ── Sync with dark/light mode toggle ──────────────────────
            new MutationObserver(mutations => {
                mutations.forEach(m => {
                    if (m.attributeName === 'class') location.reload();
                });
            }).observe(document.documentElement, { attributes: true });
        });
    </script>
    @endpush
</x-layouts.app>
