<x-layouts.app :title="'Notifikasi'">

    <div class="max-w-2xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h2 class="text-xl font-bold text-slate-900 dark:text-white">Notifikasi</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5">Semua notifikasi dalam aplikasi</p>
            </div>
        </div>

        <div class="bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl overflow-hidden">
            @if($notifications->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-slate-400 dark:text-slate-500">
                <svg class="w-12 h-12 mb-3 opacity-40" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                </svg>
                <p class="text-sm">Tidak ada notifikasi</p>
            </div>
            @else
            <div class="divide-y divide-slate-100 dark:divide-slate-800">
                @foreach($notifications as $notif)
                <a href="{{ route('deploy-requests.show', $notif->deployRequest) }}"
                   class="flex items-start gap-4 px-5 py-4 hover:bg-slate-50 dark:hover:bg-slate-800/50 transition-colors
                          {{ !$notif->is_read ? 'bg-indigo-50 dark:bg-indigo-500/5' : '' }}">
                    <div class="w-2 h-2 rounded-full mt-2 flex-shrink-0
                                {{ !$notif->is_read ? 'bg-indigo-500' : 'bg-slate-300 dark:bg-slate-700' }}"></div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-slate-900 dark:text-white">{{ $notif->title }}</p>
                        <p class="text-sm text-slate-500 dark:text-slate-400 mt-0.5 line-clamp-2">{{ $notif->message }}</p>
                        <p class="text-xs text-slate-400 dark:text-slate-600 mt-1">{{ $notif->created_at->diffForHumans() }}</p>
                    </div>
                </a>
                @endforeach
            </div>

            @if($notifications->hasPages())
            <div class="px-5 py-4 border-t border-slate-200 dark:border-slate-800">
                {{ $notifications->links() }}
            </div>
            @endif
            @endif
        </div>
    </div>

</x-layouts.app>
