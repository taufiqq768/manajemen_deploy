<x-layouts.app>
    <x-slot name="title">IT Work Hub - Dashboard</x-slot>

    <div class="flex flex-col items-center justify-center py-20 text-center">
        <div class="w-24 h-24 bg-slate-100 dark:bg-slate-800 rounded-full flex items-center justify-center mb-6">
            <i class="ti ti-chart-bar text-4xl text-slate-400"></i>
        </div>
        <h2 class="text-2xl font-bold text-slate-800 dark:text-white mb-2">Dashboard IT Work Hub</h2>
        <p class="text-slate-500 dark:text-slate-400 max-w-md mx-auto mb-8">
            Modul ini masih dalam tahap pengembangan (Fase 2). Nantinya, halaman ini akan menampilkan visualisasi data dan statistik project secara keseluruhan.
        </p>
        <a href="{{ route('it-work-hub.longlist') }}" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-medium rounded-lg shadow-sm transition-colors flex items-center gap-2">
            <i class="ti ti-code"></i> Ke Halaman App Dev
        </a>
    </div>
</x-layouts.app>
