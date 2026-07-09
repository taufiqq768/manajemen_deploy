<x-layouts.app>
    <x-slot name="title">Tambah Project Non App - IT Work Hub</x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('it-work-hub.non-app.longlist') }}"
                class="p-2 text-slate-400 hover:text-slate-600 dark:hover:text-slate-300 transition-colors bg-white dark:bg-slate-900 rounded-lg shadow-sm border border-slate-200 dark:border-slate-800">
                <i class="ti ti-arrow-left text-xl"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-slate-800 dark:text-white">Tambah Project Non App</h2>
                <p class="text-sm text-slate-500 dark:text-slate-400 mt-1">Daftarkan project Non App baru
                    ke IT Work Hub.</p>
            </div>
        </div>

        <div
            class="bg-white dark:bg-slate-900 rounded-xl shadow-sm border border-slate-200 dark:border-slate-800 overflow-hidden">
            <form action="{{ route('it-work-hub.non-app.store') }}" method="POST" class="p-6 space-y-8">
                @csrf

                {{-- Flash Error --}}
                @if ($errors->any())
                    <div class="p-4 bg-red-50 dark:bg-red-900/20 text-red-600 dark:text-red-400 rounded-lg text-sm mb-6">
                        <ul class="list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Bagian 1: Informasi Umum --}}
                <div class="space-y-4">
                    <h3
                        class="text-lg font-semibold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">
                        1. Informasi Umum</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Nama Project <span
                                    class="text-red-500">*</span></label>
                            <input type="text" name="name" required value="{{ old('name') }}"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Contoh: Aplikasi Absensi Wajah">
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian Singkat</label>
                            <textarea rows="3" name="description"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Deskripsi singkat tentang project...">{{ old('description') }}</textarea>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Priority <span class="text-red-500">*</span></label>
                            <select name="priority" required
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                <option value="High" {{ old('priority') == 'High' ? 'selected' : '' }}>High</option>
                                <option value="Medium" {{ old('priority') == 'Medium' ? 'selected' : '' }}>Medium</option>
                                <option value="Low" {{ old('priority') == 'Low' ? 'selected' : '' }}>Low</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Status Awal <span class="text-red-500">*</span></label>
                            <select name="status" required
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                                <option value="Not Started" {{ old('status') == 'Not Started' ? 'selected' : '' }}>Not Started</option>
                                <option value="Live" {{ old('status') == 'Live' ? 'selected' : '' }}>Live</option>
                                <option value="Hold" {{ old('status') == 'Hold' ? 'selected' : '' }}>Hold</option>
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Squad / Tim <span class="text-red-500">*</span> <span
                                    class="font-normal text-slate-400 text-xs">(Gunakan Ctrl/Cmd untuk pilih lebih dari 1)</span></label>
                            
                            <select name="squads[]" multiple required class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922] h-32">
                                @foreach($users as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ ucfirst($user->role) }})</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Business Process Owner
                                (BPO)</label>
                            <input type="text" name="bpo" value="{{ old('bpo') }}"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Divisi terkait">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Progress Awal
                                (%) <span class="text-red-500">*</span></label>
                            <input type="number" name="progress" min="0" max="100" required value="{{ old('progress', 0) }}"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Tanggal Inisiasi</label>
                            <input type="date" name="start_date" value="{{ old('start_date') }}"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Deadline</label>
                            <input type="date" name="deadline" value="{{ old('deadline') }}"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>

                        <div class="space-y-1">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Deadline Penyesuaian</label>
                            <input type="date" name="adjustment_date" value="{{ old('adjustment_date') }}"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]">
                        </div>
                    </div>
                </div>

                {{-- Bagian 2: Pain Point --}}
                <div class="space-y-4 pt-4">
                    <h3
                        class="text-lg font-semibold text-slate-800 dark:text-white border-b border-slate-200 dark:border-slate-800 pb-2">
                        2. Analisis Pain Point</h3>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Uraian Pain
                                Point</label>
                            <textarea rows="3" name="pain_point_uraian"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Apa masalah yang ingin diselesaikan?">{{ old('pain_point_uraian') }}</textarea>
                        </div>

                        <div class="space-y-1 md:col-span-2">
                            <label class="text-sm font-medium text-slate-700 dark:text-slate-300">Impact
                                (Dampak)</label>
                            <textarea rows="3" name="pain_point_impact"
                                class="w-full rounded-lg border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-950 text-sm focus:border-[#639922] focus:ring-[#639922]"
                                placeholder="Dampak dari pain point tersebut">{{ old('pain_point_impact') }}</textarea>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end gap-3 pt-6 border-t border-slate-200 dark:border-slate-800">
                    <a href="{{ route('it-work-hub.non-app.longlist') }}"
                        class="px-5 py-2.5 bg-white dark:bg-slate-800 border border-slate-300 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-medium rounded-lg shadow-sm hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors">
                        Batal
                    </a>
                    <button type="submit"
                        class="px-5 py-2.5 bg-[#639922] hover:bg-[#3B6D11] text-white font-medium rounded-lg shadow-sm transition-colors">
                        Simpan Project
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.app>