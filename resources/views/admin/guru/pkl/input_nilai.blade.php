<div class="flex flex-col h-full bg-slate-900 rounded-2xl">
    <!-- Student Header (Sticky Top) -->
    <div
        class="flex-none px-6 py-5 border-b border-slate-700/50 bg-slate-900/95 backdrop-blur-md sticky top-0 z-10 rounded-t-2xl">
        <div class="flex items-center gap-4">
            <div
                class="w-14 h-14 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl shadow-lg shadow-blue-500/20">
                {{ substr($pkl->student->name, 0, 1) }}
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">{{ $pkl->student->name }}</h3>
                <div class="flex items-center gap-2 text-sm text-slate-400 mt-1">
                    <span>{{ $pkl->student->nis }}</span>
                    <span class="w-1 h-1 rounded-full bg-slate-600"></span>
                    <span>{{ $pkl->student->kelas->nm_kls ?? '-' }}</span>
                </div>
                <p class="text-blue-400 text-xs mt-1 font-medium">{{ $pkl->dudi->nama }}</p>
            </div>
        </div>
    </div>

    <!-- Content -->
    <div class="p-6">
        <form id="gradeForm" action="{{ route('admin.guru.pkl.store_nilai', $pkl->id) }}" method="POST">
            @csrf

            <div class="grid grid-cols-1 gap-8">
                <!-- Soft Skills Section -->
                <div class="bg-slate-800/20 border border-slate-700/50 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 bg-slate-800/50 border-b border-slate-700/50 flex items-center gap-3">
                        <span class="w-2 h-6 rounded-full bg-emerald-500 shadow-[0_0_10px_rgba(16,185,129,0.3)]"></span>
                        <h4 class="font-semibold text-white">Soft Skills</h4>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($kompSoft as $komp)
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wide">{{ $komp->nama }}</label>
                                <div class="relative group">
                                    <input type="number" name="nilai_soft[{{ $komp->id }}]"
                                        value="{{ $softNilai[$komp->id] ?? '' }}" min="0" max="100"
                                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-emerald-500/50 focus:border-emerald-500 placeholder-slate-600 transition-all text-sm group-hover:border-slate-600"
                                        placeholder="0-100">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Hard Skills Section -->
                <div class="bg-slate-800/20 border border-slate-700/50 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 bg-slate-800/50 border-b border-slate-700/50 flex items-center gap-3">
                        <span class="w-2 h-6 rounded-full bg-blue-500 shadow-[0_0_10px_rgba(59,130,246,0.3)]"></span>
                        <h4 class="font-semibold text-white">Hard Skills</h4>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($kompHard as $komp)
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wide">{{ $komp->nama }}</label>
                                <div class="relative group">
                                    <input type="number" name="nilai_hard[{{ $komp->id }}]"
                                        value="{{ $hardNilai[$komp->id] ?? '' }}" min="0" max="100"
                                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-blue-500/50 focus:border-blue-500 placeholder-slate-600 transition-all text-sm group-hover:border-slate-600"
                                        placeholder="0-100">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Wirausaha Section -->
                <div class="bg-slate-800/20 border border-slate-700/50 rounded-xl overflow-hidden">
                    <div class="px-5 py-3 bg-slate-800/50 border-b border-slate-700/50 flex items-center gap-3">
                        <span class="w-2 h-6 rounded-full bg-purple-500 shadow-[0_0_10px_rgba(168,85,247,0.3)]"></span>
                        <h4 class="font-semibold text-white">Kewirausahaan</h4>
                    </div>
                    <div class="p-5 grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($kompWirausaha as $komp)
                            <div>
                                <label
                                    class="block text-xs font-medium text-slate-400 mb-1.5 uppercase tracking-wide">{{ $komp->nama }}</label>
                                <div class="relative group">
                                    <input type="number" name="nilai_wirausaha[{{ $komp->id }}]"
                                        value="{{ $wirausahaNilai[$komp->id] ?? '' }}" min="0" max="100"
                                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700 rounded-lg text-white focus:ring-2 focus:ring-purple-500/50 focus:border-purple-500 placeholder-slate-600 transition-all text-sm group-hover:border-slate-600"
                                        placeholder="0-100">
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Footer Actions (Sticky Bottom) -->
    <div
        class="flex-none px-6 py-4 border-t border-slate-700/50 bg-slate-900/95 backdrop-blur-md flex justify-end gap-3 sticky bottom-0 z-10 rounded-b-2xl">
        <button type="button" @click="showGradeModal = false"
            class="px-5 py-2.5 rounded-xl border border-slate-600 text-slate-300 hover:bg-slate-800 hover:text-white transition-colors font-medium text-sm">
            Batal
        </button>
        <button type="button" onclick="document.getElementById('gradeForm').submit()"
            class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-blue-600 to-purple-600 text-white font-medium hover:from-blue-700 hover:to-purple-700 transition-all shadow-lg shadow-blue-500/25 text-sm flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            Simpan Nilai
        </button>
    </div>
</div>