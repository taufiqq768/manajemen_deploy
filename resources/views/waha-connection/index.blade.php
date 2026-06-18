<x-app-layout>
    <x-slot name="title">
        Cek Koneksi WAHA
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="bg-white dark:bg-slate-900 shadow-sm border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
            <div class="px-6 py-5 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-semibold text-slate-900 dark:text-white">Status Koneksi WhatsApp API (WAHA)</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Mengecek konektivitas ke layanan WhatsApp API.</p>
                </div>
                <div>
                    <a href="{{ route('waha-connection.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-medium rounded-lg transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-slate-900">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                        </svg>
                        Refresh
                    </a>
                </div>
            </div>

            <div class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Config Info -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Konfigurasi .env</h4>
                        
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 space-y-3 border border-slate-100 dark:border-slate-700">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">WAHA URL</span>
                                <span class="block text-sm text-slate-900 dark:text-white font-mono mt-0.5 break-all">{{ $wahaUrl ?: 'Tidak dikonfigurasi' }}</span>
                            </div>
                            <div>
                                <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">WAHA Session Name</span>
                                <span class="block text-sm text-slate-900 dark:text-white font-mono mt-0.5">{{ $wahaSession }}</span>
                            </div>
                        </div>
                    </div>

                    <!-- Connection Status -->
                    <div class="space-y-4">
                        <h4 class="text-sm font-semibold text-slate-800 dark:text-slate-200 uppercase tracking-wider">Hasil Pengecekan</h4>
                        
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-4 space-y-3 border border-slate-100 dark:border-slate-700">
                            <div>
                                <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Status</span>
                                <div class="mt-1.5">
                                    @if($status === 'WORKING')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium bg-green-500/10 text-green-700 dark:text-green-400 border border-green-500/20">
                                            <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                                            WORKING (Terhubung)
                                        </span>
                                    @elseif($status === 'STOPPED')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium bg-red-500/10 text-red-700 dark:text-red-400 border border-red-500/20">
                                            <span class="w-2 h-2 rounded-full bg-red-500"></span>
                                            STOPPED (Berhenti)
                                        </span>
                                    @elseif($status === 'SCAN_QR_CODE' || $status === 'STARTING')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium bg-yellow-500/10 text-yellow-700 dark:text-yellow-400 border border-yellow-500/20">
                                            <span class="w-2 h-2 rounded-full bg-yellow-500 animate-pulse"></span>
                                            {{ $status }} (Menunggu Scan QR/Starting)
                                        </span>
                                    @elseif($status === 'error')
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium bg-red-500/10 text-red-700 dark:text-red-400 border border-red-500/20">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            Gagal Terhubung
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md text-sm font-medium bg-slate-500/10 text-slate-700 dark:text-slate-400 border border-slate-500/20">
                                            <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                            {{ strtoupper($status) }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                            
                            <div>
                                <span class="block text-xs font-medium text-slate-500 dark:text-slate-400">Pesan Detail</span>
                                <span class="block text-sm text-slate-900 dark:text-white mt-0.5 {{ $status === 'error' ? 'text-red-600 dark:text-red-400' : '' }}">
                                    {{ $message }}
                                </span>
                            </div>

                            @if($sessionData)
                                <div>
                                    <span class="block text-xs font-medium text-slate-500 dark:text-slate-400 mb-1">Raw Session Data (JSON)</span>
                                    <div class="bg-slate-900 rounded-md p-3 overflow-x-auto text-left">
                                        <pre class="text-xs text-green-400 font-mono m-0">@json($sessionData, JSON_PRETTY_PRINT)</pre>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
                
                @if($status === 'error')
                    <div class="mt-6 bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                        <div class="flex items-start">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800 dark:text-yellow-300">
                                    Troubleshooting
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700 dark:text-yellow-400 space-y-1">
                                    <p>1. Pastikan server WAHA sedang berjalan (running) pada URL <code>{{ $wahaUrl }}</code>.</p>
                                    <p>2. Pastikan server aplikasi (Manajemen Deploy) memiliki akses jaringan ke URL WAHA tersebut.</p>
                                    <p>3. Jika menggunakan Docker, periksa log container WAHA.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
                
                <!-- Uptime & Chart Section -->
                <div class="mt-8 border-t border-slate-200 dark:border-slate-800 pt-8">
                    <div class="flex items-center justify-between mb-6">
                        <h4 class="text-base font-semibold text-slate-800 dark:text-slate-200">Riwayat Koneksi (24 Jam Terakhir)</h4>
                        <div class="text-sm">
                            <span class="text-slate-500 dark:text-slate-400">Uptime: </span>
                            <span class="font-bold {{ $uptimePercentage >= 95 ? 'text-green-500' : ($uptimePercentage >= 80 ? 'text-yellow-500' : 'text-red-500') }}">
                                {{ $uptimePercentage }}%
                            </span>
                        </div>
                    </div>
                    
                    @if(count($chartData) > 0)
                        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-lg p-4">
                            <div id="wahaChart" class="w-full h-64"></div>
                        </div>
                    @else
                        <div class="bg-slate-50 dark:bg-slate-800/50 rounded-lg p-6 text-center text-sm text-slate-500 dark:text-slate-400 border border-slate-100 dark:border-slate-700">
                            Belum ada data riwayat koneksi. Sistem akan mencatat riwayat setiap 5 menit.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- ApexCharts CDN -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        // Auto-refresh halaman setiap 60 detik agar data selalu uptodate
        setTimeout(function() {
            window.location.reload();
        }, 60000);

        document.addEventListener('DOMContentLoaded', function () {
            const chartData = @json($chartData ?? []);
            
            if (chartData.length > 0) {
                const isDarkMode = document.documentElement.classList.contains('dark') || localStorage.getItem('theme') === 'dark';
                
                const options = {
                    series: [{
                        name: 'Status',
                        data: chartData
                    }],
                    chart: {
                        type: 'area',
                        height: 250,
                        toolbar: { show: false },
                        animations: { enabled: false },
                        background: 'transparent'
                    },
                    colors: ['#10b981'],
                    fill: {
                        type: 'gradient',
                        gradient: {
                            shadeIntensity: 1,
                            opacityFrom: 0.4,
                            opacityTo: 0.05,
                            stops: [0, 100]
                        }
                    },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'stepline', width: 2 },
                    xaxis: {
                        type: 'datetime',
                        min: new Date().getTime() - (24 * 60 * 60 * 1000), // 24 hours ago
                        max: new Date().getTime(), // now
                        labels: {
                            style: { colors: isDarkMode ? '#94a3b8' : '#64748b' },
                            datetimeUTC: false
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        min: 0,
                        max: 1.2,
                        tickAmount: 1,
                        labels: {
                            formatter: function (value) {
                                if (value === 1) return "UP";
                                if (value === 0) return "DOWN";
                                return "";
                            },
                            style: { colors: isDarkMode ? '#94a3b8' : '#64748b' }
                        }
                    },
                    grid: {
                        borderColor: isDarkMode ? '#334155' : '#e2e8f0',
                        strokeDashArray: 4,
                        yaxis: { lines: { show: true } }
                    },
                    tooltip: {
                        theme: isDarkMode ? 'dark' : 'light',
                        x: { format: 'dd MMM yyyy HH:mm' },
                        y: {
                            formatter: function(value) {
                                return value === 1 ? 'WORKING' : 'DOWN/ERROR';
                            }
                        }
                    }
                };

                const chart = new ApexCharts(document.querySelector("#wahaChart"), options);
                chart.render();
            }
        });
    </script>
</x-app-layout>
