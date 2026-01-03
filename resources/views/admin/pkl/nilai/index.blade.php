@extends('layouts.admin')

@section('title', 'Penilaian PKL')

@section('content')
    <div class="space-y-6"
        x-data="{ 
                                                                                                                                                        showDetailModal: false,
                                                                                                                                                        detailData: {},
                                                                                                                                                        showAddModal: false,
                                                                                                                                                        showEditModal: false,
                                                                                                                                                        editData: {},
                                                                                                                                                        editType: 'soft', 
                                                                                                                                                        selectedKelas: '',
                                                                                                                                                        students: [],
                                                                                                                                                        activeTab: '{{ request('tab', 'soft') }}',
                                                                                                                                                        currentStudentName: ''
                                                                                                                                                    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Penilaian PKL</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola nilai komponen soft skill dan hard skill siswa PKL</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- TP Setting Combobox -->
                <form action="{{ route('admin.pkl.nilai.index') }}" method="GET" class="flex items-center gap-2">
                    <input type="hidden" name="tab" value="{{ request('tab', 'soft') }}">
                    <label class="text-sm text-slate-400">TP Aktif:</label>
                    <select name="tp_id" onchange="this.form.submit()"
                        class="px-3 py-2 bg-indigo-500/20 border border-indigo-500/30 rounded-lg text-indigo-300 text-sm font-medium cursor-pointer">
                        @foreach($tpList as $tp)
                            <option value="{{ $tp->id }}" {{ $tpId == $tp->id ? 'selected' : '' }}
                                class="bg-slate-800 text-white">
                                {{ $tp->nm_tp }} {{ $tp->is_active ? '(Aktif)' : '' }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <button @click="showAddModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah Nilai</span>
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-slate-400">Total Data Nilai</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ number_format($stats['avgNilai'], 1) }}</p>
                        <p class="text-xs text-slate-400">Rata-rata Nilai (Gabungan)</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-slate-700/50 pb-2">
            <button @click="activeTab = 'soft'; document.getElementById('tabInput').value = 'soft'"
                :class="activeTab === 'soft' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'text-slate-400 hover:text-white border-transparent'"
                class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all border cursor-pointer">
                Nilai Soft Skill
            </button>
            <button @click="activeTab = 'hard'; document.getElementById('tabInput').value = 'hard'"
                :class="activeTab === 'hard' ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'text-slate-400 hover:text-white border-transparent'"
                class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all border cursor-pointer">
                Nilai Hard Skill
            </button>
            <button @click="activeTab = 'wirausaha'; document.getElementById('tabInput').value = 'wirausaha'"
                :class="activeTab === 'wirausaha' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'text-slate-400 hover:text-white border-transparent'"
                class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all border cursor-pointer">
                Nilai Aspek Berwirausaha
            </button>
        </div>


        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-3">
            <form method="GET" action="{{ route('admin.pkl.nilai.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tab" id="tabInput" value="{{ request('tab', 'soft') }}">
                <input type="hidden" name="tp_id" value="{{ $tpId }}">
                <select name="dudi_id" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                    <option value="">Semua DUDI</option>
                    @foreach($dudiList as $dudi)
                        <option value="{{ $dudi->id }}" {{ ($dudiId ?? '') == $dudi->id ? 'selected' : '' }}>
                            {{ $dudi->nama }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/NIS..."
                    class="w-44 px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                <select name="per_page" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="30" {{ $perPage == 30 ? 'selected' : '' }}>30</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="70" {{ $perPage == 70 ? 'selected' : '' }}>70</option>
                    <option value="100" {{ $perPage == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>Semua</option>
                </select>
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors cursor-pointer text-sm">
                    Cari
                </button>
            </form>
        </div>

        <!-- Soft Skill Nilai Table -->
        <div x-show="activeTab === 'soft'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="rounded-xl bg-slate-800/50 border border-emerald-500/30 overflow-hidden mb-6">
            <div class="flex items-center gap-3 p-4 border-b border-slate-700/50 bg-emerald-500/5">
                <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-emerald-400">Nilai Komponen Soft Skill</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase w-24">Rata-rata
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @php $currentDudiId = null; @endphp
                        @forelse($softNilaiList as $index => $pkl)
                            @if($currentDudiId !== $pkl->dudi_id)
                                @php $currentDudiId = $pkl->dudi_id; @endphp
                                <tr class="bg-slate-700/40">
                                    <td colspan="5" class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-bold text-white text-sm">{{ $pkl->dudi->nama ?? 'Tanpa DUDI' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">
                                    <p class="text-sm font-medium text-white">{{ $pkl->student->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $pkl->student->nis ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-2 text-sm text-slate-300">{{ $pkl->student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($pkl->softNilai->count() > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-semibold
                                            {{ ($pkl->softNilai->avg('nilai') ?? 0) >= 80 ? 'bg-emerald-500/20 text-emerald-400' : (($pkl->softNilai->avg('nilai') ?? 0) >= 60 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                                            {{ number_format($pkl->softNilai->avg('nilai') ?? 0, 1) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                            Belum Input Nilai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('admin.pkl.nilai.show', ['pklId' => $pkl->id, 'type' => 'soft']) }}"
                                        class="px-3 py-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 rounded-lg text-xs font-medium transition-colors cursor-pointer border border-blue-500/30 inline-block">
                                        {{ $pkl->softNilai->count() > 0 ? 'Detail Nilai' : 'Input Nilai' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada data nilai Soft Skill
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $softNilaiList->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

        <!-- Hard Skill Nilai Table -->
        <div x-show="activeTab === 'hard'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="rounded-xl bg-slate-800/50 border border-blue-500/30 overflow-hidden" style="display: none;">
            <!-- Hidden by default handled by x-show -->
            <div class="flex items-center gap-3 p-4 border-b border-slate-700/50 bg-blue-500/5">
                <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-blue-400">Nilai Komponen Hard Skill</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase w-24">Rata-rata
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @php $currentDudiId = null; @endphp
                        @forelse($hardNilaiList as $index => $pkl)
                            @if($currentDudiId !== $pkl->dudi_id)
                                @php $currentDudiId = $pkl->dudi_id; @endphp
                                <tr class="bg-slate-700/40">
                                    <td colspan="5" class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-blue-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-bold text-white text-sm">{{ $pkl->dudi->nama ?? 'Tanpa DUDI' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">
                                    <p class="text-sm font-medium text-white">{{ $pkl->student->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $pkl->student->nis ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-2 text-sm text-slate-300">{{ $pkl->student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($pkl->hardNilai->count() > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-semibold
                                            {{ ($pkl->hardNilai->avg('nilai') ?? 0) >= 80 ? 'bg-emerald-500/20 text-emerald-400' : (($pkl->hardNilai->avg('nilai') ?? 0) >= 60 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                                            {{ number_format($pkl->hardNilai->avg('nilai') ?? 0, 1) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                            Belum Input Nilai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('admin.pkl.nilai.show', ['pklId' => $pkl->id, 'type' => 'hard']) }}"
                                        class="px-3 py-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 rounded-lg text-xs font-medium transition-colors cursor-pointer border border-blue-500/30 inline-block">
                                        {{ $pkl->hardNilai->count() > 0 ? 'Detail Nilai' : 'Input Nilai' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada data nilai Hard Skill
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $hardNilaiList->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

        <!-- Wirausaha Nilai Table -->
        <div x-show="activeTab === 'wirausaha'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
            class="rounded-xl bg-slate-800/50 border border-amber-500/30 overflow-hidden" style="display: none;">
            <div class="flex items-center gap-3 p-4 border-b border-slate-700/50 bg-amber-500/5">
                <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h3 class="text-lg font-semibold text-amber-400">Nilai Aspek Berwirausaha</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase w-24">Rata-rata
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase w-24">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @php $currentDudiId = null; @endphp
                        @forelse($wirausahaNilaiList as $index => $pkl)
                            @if($currentDudiId !== $pkl->dudi_id)
                                @php $currentDudiId = $pkl->dudi_id; @endphp
                                <tr class="bg-slate-700/40">
                                    <td colspan="5" class="px-4 py-2">
                                        <div class="flex items-center gap-2">
                                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                            <span class="font-bold text-white text-sm">{{ $pkl->dudi->nama ?? 'Tanpa DUDI' }}</span>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-2">
                                    <p class="text-sm font-medium text-white">{{ $pkl->student->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $pkl->student->nis ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-2 text-sm text-slate-300">{{ $pkl->student->kelas->nm_kls ?? '-' }}</td>
                                <td class="px-4 py-2 text-center">
                                    @if($pkl->wirausahaNilai->count() > 0)
                                        <span
                                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-sm font-semibold
                                            {{ ($pkl->wirausahaNilai->avg('nilai') ?? 0) >= 80 ? 'bg-emerald-500/20 text-emerald-400' : (($pkl->wirausahaNilai->avg('nilai') ?? 0) >= 60 ? 'bg-yellow-500/20 text-yellow-400' : 'bg-red-500/20 text-red-400') }}">
                                            {{ number_format($pkl->wirausahaNilai->avg('nilai') ?? 0, 1) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                            Belum Input Nilai
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-center">
                                    <a href="{{ route('admin.pkl.nilai.show', ['pklId' => $pkl->id, 'type' => 'wirausaha']) }}"
                                        class="px-3 py-1.5 bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 rounded-lg text-xs font-medium transition-colors cursor-pointer border border-amber-500/30 inline-block">
                                        {{ $pkl->wirausahaNilai->count() > 0 ? 'Detail Nilai' : 'Input Nilai' }}
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-8 text-center text-slate-400">
                                    Belum ada data nilai Aspek Berwirausaha
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $wirausahaNilaiList->appends(request()->all())->links() }}
                </div>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showAddModal = false"></div>
                <div class="relative bg-slate-900 rounded-2xl border border-slate-700/50 w-full max-w-4xl p-6 shadow-2xl max-h-[90vh] overflow-y-auto"
                    x-data="{ 
                                            selectedDudi: '', 
                                            selectedStudent: '',
                                            studentJurusan: null,
                                            pimpinan: '',
                                            pembimbingIndustri: '',
                                            pklData: {{ Js::from($pklList) }},
                                            gradedStudentIds: {{ Js::from($gradedStudentIds) }},
                                            kompSoftData: {{ Js::from($kompSoftList) }},
                                            kompHardData: {{ Js::from($kompHardList) }},
                                            kompWirausahaData: {{ Js::from($kompWirausahaList) }},
                                            getFilteredStudents() {
                                                return this.pklData.filter(p => p.dudi_id == this.selectedDudi && !this.gradedStudentIds.includes(p.student_id));
                                            },
                                            getFilteredKompSoft() {
                                                if (!this.studentJurusan) return [];
                                                return this.kompSoftData.filter(k => k.m_jurusan_id == this.studentJurusan);
                                            },
                                            getFilteredKompHard() {
                                                if (!this.studentJurusan) return [];
                                                return this.kompHardData.filter(k => k.m_jurusan_id == this.studentJurusan);
                                            },
                                            getFilteredKompWirausaha() {
                                                if (!this.studentJurusan) return [];
                                                return this.kompWirausahaData.filter(k => k.m_jurusan_id == this.studentJurusan);
                                            },
                                            updateDudiInfo() {
                                                // Find existing PKL data for selected DUDI to get pimpinan & pembimbing
                                                let pklForDudi = this.pklData.find(p => p.dudi_id == this.selectedDudi && (p.pimpinan || p.pembimbing_industri));
                                                if (pklForDudi) {
                                                    this.pimpinan = pklForDudi.pimpinan || '';
                                                    this.pembimbingIndustri = pklForDudi.pembimbing_industri || '';
                                                } else {
                                                    this.pimpinan = '';
                                                    this.pembimbingIndustri = '';
                                                }
                                                // Reset student selection
                                                this.selectedStudent = '';
                                                this.studentJurusan = null;
                                            },
                                            updateJurusan() {
                                                let pkl = this.pklData.find(p => p.student_id == this.selectedStudent);
                                                if (pkl && pkl.student) {
                                                    this.studentJurusan = pkl.student.m_jurusan_id;
                                                } else {
                                                    this.studentJurusan = null;
                                                }
                                            }
                                        }">
                    <h3 class="text-xl font-bold text-white mb-6">Tambah Nilai PKL</h3>
                    <form action="{{ route('admin.pkl.nilai.store') }}" method="POST">
                        @csrf
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">DUDI <span
                                        class="text-red-400">*</span></label>
                                <select x-model="selectedDudi" @change="updateDudiInfo()"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih DUDI</option>
                                    @foreach($dudiList as $dudi)
                                        <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Siswa <span
                                        class="text-red-400">*</span></label>
                                <select name="student_id" required x-model="selectedStudent" @change="updateJurusan()"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Siswa</option>
                                    <template x-for="pkl in getFilteredStudents()" :key="pkl.id">
                                        <option :value="pkl.student_id"
                                            x-text="pkl.student ? (pkl.student.name + ' - ' + (pkl.student.kelas ? pkl.student.kelas.nm_kls : '')) : '-'">
                                        </option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Pimpinan DUDI</label>
                                <input type="text" name="pimpinan" x-model="pimpinan" placeholder="Nama Pimpinan DUDI"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Pembimbing Industri</label>
                                <input type="text" name="pembimbing_industri" x-model="pembimbingIndustri"
                                    placeholder="Nama Pembimbing Industri"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>
                        </div>

                        <!-- Komponen Soft Skill -->
                        <div x-show="studentJurusan && getFilteredKompSoft().length > 0" class="mb-6">
                            <h4 class="text-lg font-semibold text-emerald-400 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Komponen Soft Skill
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="komp in getFilteredKompSoft()" :key="komp.id">
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/30 border border-slate-700/30">
                                        <label class="flex-1 text-sm text-slate-300" x-text="komp.nama"></label>
                                        <input type="number" :name="'nilai_soft[' + komp.id + ']'" step="0.01" min="0"
                                            max="100" placeholder="0-100"
                                            class="w-24 px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm text-center">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Komponen Hard Skill -->
                        <div x-show="studentJurusan && getFilteredKompHard().length > 0" class="mb-6">
                            <h4 class="text-lg font-semibold text-blue-400 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                Komponen Hard Skill
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="komp in getFilteredKompHard()" :key="komp.id">
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/30 border border-slate-700/30">
                                        <label class="flex-1 text-sm text-slate-300" x-text="komp.nama"></label>
                                        <input type="number" :name="'nilai_hard[' + komp.id + ']'" step="0.01" min="0"
                                            max="100" placeholder="0-100"
                                            class="w-24 px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm text-center">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Komponen Wirausaha -->
                        <div x-show="studentJurusan && getFilteredKompWirausaha().length > 0" class="mb-6">
                            <h4 class="text-lg font-semibold text-amber-400 mb-4 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Komponen Aspek Berwirausaha
                            </h4>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                <template x-for="komp in getFilteredKompWirausaha()" :key="komp.id">
                                    <div
                                        class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/30 border border-slate-700/30">
                                        <label class="flex-1 text-sm text-slate-300" x-text="komp.nama"></label>
                                        <input type="number" :name="'nilai_wirausaha[' + komp.id + ']'" step="0.01" min="0"
                                            max="100" placeholder="0-100"
                                            class="w-24 px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-sm text-center">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Empty state -->
                        <div x-show="selectedStudent && (!studentJurusan || (getFilteredKompSoft().length === 0 && getFilteredKompHard().length === 0 && getFilteredKompWirausaha().length === 0))"
                            class="text-center py-8">
                            <p class="text-slate-400">Tidak ada komponen penilaian untuk jurusan siswa ini</p>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                            <button type="button" @click="showAddModal = false"
                                class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                            <button type="submit"
                                x-show="studentJurusan && (getFilteredKompSoft().length > 0 || getFilteredKompHard().length > 0 || getFilteredKompWirausaha().length > 0)"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showEditModal = false"></div>
                <div class="relative bg-slate-900 rounded-2xl border border-slate-700/50 w-full max-w-4xl p-6 shadow-2xl">
                    <h3 class="text-xl font-bold text-white mb-6">Edit Nilai PKL (<span
                            x-text="editType === 'soft' ? 'Soft Skill' : 'Hard Skill'"></span>)</h3>
                    <form :action="`{{ url('/admin/pkl/nilai') }}/${editData.id}`" method="POST">
                        @csrf
                        @method('PUT')
                        <!-- Hidden input for Type -->
                        <input type="hidden" name="type" x-model="editType">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6"
                            x-data="{ 
                                                                                                                                                selectedDudiEdit: '', 
                                                                                                                                                pklData: {{ Js::from($pklList) }} 
                                                                                                                                            }"
                            x-init="$watch('editData', () => { 
                                                                                                                                                if(editData && pklData) {
                                                                                                                                                    // pkl is tricky because we might not have it in editData relation if not loaded or structured differently
                                                                                                                                                    // But let's try to assume we can find it via student
                                                                                                                                                    let pkl = pklData.find(p => p.student_id == editData.student_id);
                                                                                                                                                    if(pkl) selectedDudiEdit = pkl.dudi_id;
                                                                                                                                                }
                                                                                                                                            })">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">DUDI <span
                                        class="text-red-400">*</span></label>
                                <select x-model="selectedDudiEdit" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih DUDI</option>
                                    @foreach($dudiList as $dudi)
                                        <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Siswa <span
                                        class="text-red-400">*</span></label>
                                <select name="student_id" required x-model="editData.student_id"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Siswa</option>
                                    <template x-for="pkl in pklData.filter(p => p.dudi_id == selectedDudiEdit)"
                                        :key="pkl.id">
                                        <option :value="pkl.student_id"
                                            x-text="pkl.student ? (pkl.student.name + ' - ' + (pkl.student.kelas ? pkl.student.kelas.nm_kls : '')) : '-'">
                                        </option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nilai <span
                                        class="text-red-400">*</span></label>
                                <input type="number" name="nilai" step="0.01" min="0" max="100" required
                                    x-model="editData.nilai"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>

                            <!-- Soft Skill Select -->
                            <div x-show="editType === 'soft'">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Komponen Soft Skill</label>
                                <select name="pkl_kompsoft_id" x-model="editData.pkl_kompsoft_id"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Komponen</option>
                                    @foreach($kompSoftList->groupBy(fn($k) => $k->jurusan->paket_keahlian ?? 'Lainnya') as $jurusanName => $komponens)
                                        <optgroup label="{{ $jurusanName }}">
                                            @foreach($komponens as $komp)
                                                <option value="{{ $komp->id }}">{{ $komp->nama }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Hard Skill Select -->
                            <div x-show="editType === 'hard'">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Komponen Hard Skill</label>
                                <select name="pkl_komphard_id" x-model="editData.pkl_komphard_id"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Komponen</option>
                                    @foreach($kompHardList->groupBy(fn($k) => $k->jurusan->paket_keahlian ?? 'Lainnya') as $jurusanName => $komponens)
                                        <optgroup label="{{ $jurusanName }}">
                                            @foreach($komponens as $komp)
                                                <option value="{{ $komp->id }}">{{ $komp->nama }}</option>
                                            @endforeach
                                        </optgroup>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" @click="showEditModal = false"
                                class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif
    <!-- Detail Modal -->
    <div x-show="showDetailModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" role="dialog"
        aria-modal="true">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity bg-gray-900 bg-opacity-75" aria-hidden="true"
                @click="showDetailModal = false"></div>

            <div
                class="inline-block align-bottom bg-slate-800 rounded-xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full border border-slate-700">
                <form action="{{ route('admin.pkl.nilai.bulk_update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="type" :value="detailType">

                    <div class="px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                        <div class="flex justify-between items-start mb-4">
                            <h3 class="text-lg font-medium text-white"
                                x-text="'Detail Nilai ' + (detailType === 'soft' ? 'Soft Skill' : 'Hard Skill')">
                            </h3>
                            <button type="button" @click="showDetailModal = false" class="text-slate-400 hover:text-white">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>

                        <p class="text-sm text-slate-400 mb-4" x-text="currentStudentName"></p>

                        <div class="overflow-hidden rounded-lg border border-slate-700/50">
                            <table class="min-w-full divide-y divide-slate-700/50">
                                <thead class="bg-slate-900/50">
                                    <tr>
                                        <th class="px-4 py-2 text-left text-xs font-semibold text-slate-400 uppercase">
                                            Komponen</th>
                                        <th class="px-4 py-2 text-right text-xs font-semibold text-slate-400 uppercase">
                                            Nilai</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-700/50 bg-slate-800/30">
                                    <template x-if="detailType === 'soft'">
                                        <template x-for="item in detailData.soft_nilai" :key="item.id">
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-slate-300"
                                                    x-text="item.komponen_soft?.nama || '-'"></td>
                                                <td class="px-4 py-2 text-right">
                                                    <input type="number" step="0.01" max="100"
                                                        :name="'scores[' + item.id + ']'" x-model="item.nilai"
                                                        class="w-24 px-2 py-1 text-right bg-slate-700 border border-slate-600 rounded text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                    <template x-if="detailType === 'hard'">
                                        <template x-for="item in detailData.hard_nilai" :key="item.id">
                                            <tr>
                                                <td class="px-4 py-2 text-sm text-slate-300"
                                                    x-text="item.komponen_hard?.nama || '-'"></td>
                                                <td class="px-4 py-2 text-right">
                                                    <input type="number" step="0.01" max="100"
                                                        :name="'scores[' + item.id + ']'" x-model="item.nilai"
                                                        class="w-24 px-2 py-1 text-right bg-slate-700 border border-slate-600 rounded text-sm text-white focus:border-blue-500 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                                </td>
                                            </tr>
                                        </template>
                                    </template>
                                    <tr
                                        x-show="(detailType === 'soft' && (!detailData.soft_nilai || detailData.soft_nilai.length === 0)) || (detailType === 'hard' && (!detailData.hard_nilai || detailData.hard_nilai.length === 0))">
                                        <td colspan="2" class="px-4 py-4 text-center text-sm text-slate-500">
                                            Tidak ada data nilai detail
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-700/50 bg-slate-800">
                        <button type="submit"
                            class="w-full inline-flex justify-center rounded-lg border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Simpan Perubahan
                        </button>
                        <button type="button" @click="showDetailModal = false"
                            class="mt-3 w-full inline-flex justify-center rounded-lg border border-slate-600 shadow-sm px-4 py-2 bg-slate-700 text-base font-medium text-slate-300 hover:bg-slate-600 hover:text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-slate-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-colors">
                            Batal
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection