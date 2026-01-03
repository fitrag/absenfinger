@extends('layouts.admin')

@section('title', 'Suket PKL')

@section('content')
    <div class="space-y-6" x-data="{ activeTab: 'suket', showConfigModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Surat Keterangan PKL</h1>
                <p class="text-slate-400 text-sm mt-1">Cetak surat keterangan praktik kerja lapangan</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- TP Active Badge -->
                @if($selectedTp)
                    <span
                        class="inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-blue-500/20 border border-blue-500/30 text-blue-400 text-sm font-medium">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                        </svg>
                        {{ $selectedTp->nm_tp ?? 'Belum dipilih' }}
                    </span>
                @endif

                <button @click="showConfigModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 rounded-xl text-sm font-medium hover:bg-indigo-500/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    Konfigurasi Background
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-slate-400">Total Siswa Siap Cetak Suket</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-3">
            <form method="GET" action="{{ route('admin.pkl.suket.index') }}" class="flex flex-wrap items-center gap-3">
                <input type="hidden" name="tp_id" value="{{ $tpId }}">
                <!-- Search -->
                <div class="flex-1 min-w-[200px]">
                    <input type="text" name="search" value="{{ $search }}" placeholder="Cari siswa..."
                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder:text-slate-500">
                </div>
                <!-- Kelas Filter -->
                <select name="kelas_id" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm min-w-[140px]">
                    <option value="">Semua Kelas</option>
                    @foreach ($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>
                <!-- DUDI Filter -->
                <select name="dudi_id" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm min-w-[140px]">
                    <option value="">Semua DUDI</option>
                    @foreach ($dudiList as $dudi)
                        <option value="{{ $dudi->id }}" {{ $dudiId == $dudi->id ? 'selected' : '' }}>
                            {{ $dudi->nama }}
                        </option>
                    @endforeach
                </select>
                <!-- Per Page -->
                <select name="per_page" onchange="this.form.submit()"
                    class="px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm">
                    <option value="10" {{ $perPage == 10 ? 'selected' : '' }}>10</option>
                    <option value="25" {{ $perPage == 25 ? 'selected' : '' }}>25</option>
                    <option value="50" {{ $perPage == 50 ? 'selected' : '' }}>50</option>
                    <option value="all" {{ $perPage == 'all' ? 'selected' : '' }}>Semua</option>
                </select>
                <button type="submit"
                    class="px-4 py-2.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 rounded-xl text-sm font-medium transition-colors border border-blue-500/30 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Data Table (Grouped by DUDI) -->
        <div class="space-y-6">
            @php
                $groupedPkls = $pklList->groupBy(function($item) {
                    return $item->dudi->nama;
                });
            @endphp

            @forelse($groupedPkls as $dudiName => $pkls)
                <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl overflow-hidden">
                    <!-- Group Header -->
                    <div class="px-6 py-4 bg-slate-800/50 border-b border-slate-700/50 flex items-center gap-3">
                        <div class="p-2 bg-amber-500/10 rounded-lg">
                            <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-bold text-white">{{ $dudiName }}</h3>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-700 border border-slate-600 text-xs font-medium text-slate-300">
                            {{ $pkls->count() }} Siswa
                        </span>
                    </div>

                    <!-- Table -->
                    <div class="overflow-x-auto">
                        <table class="w-full">
                            <thead>
                                <tr class="bg-slate-800/30 border-b border-slate-700/50 text-left">
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase w-12">No</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase">Kelas</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase text-center w-32">Nilai Akhir</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase text-center w-32">Predikat</th>
                                    <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase text-center w-28">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-700/50">
                                @foreach($pkls as $pkl)
                                    @php
                                        $avgSoft = $pkl->softNilai->avg('nilai') ?? 0;
                                        $avgHard = $pkl->hardNilai->avg('nilai') ?? 0;
                                        $avgWirausaha = $pkl->wirausahaNilai->avg('nilai') ?? 0;
                                        $finalGrade = ($avgSoft * 0.40) + ($avgHard * 0.50) + ($avgWirausaha * 0.10);

                                        if ($finalGrade >= 90) {
                                            $predikat = 'Amat Baik';
                                            $predikatColor = 'bg-emerald-500/20 text-emerald-400 border-emerald-500/20';
                                        } elseif ($finalGrade >= 80) {
                                            $predikat = 'Baik';
                                            $predikatColor = 'bg-blue-500/20 text-blue-400 border-blue-500/20';
                                        } elseif ($finalGrade >= 70) {
                                            $predikat = 'Cukup';
                                            $predikatColor = 'bg-yellow-500/20 text-yellow-400 border-yellow-500/20';
                                        } elseif ($finalGrade >= 60) {
                                            $predikat = 'Kurang';
                                            $predikatColor = 'bg-orange-500/20 text-orange-400 border-orange-500/20';
                                        } else {
                                            $predikat = 'Sangat Kurang';
                                            $predikatColor = 'bg-red-500/20 text-red-400 border-red-500/20';
                                        }
                                    @endphp
                                    <tr class="hover:bg-slate-800/30 transition-colors">
                                        <td class="px-6 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                                        <td class="px-6 py-4">
                                            <div>
                                                <p class="text-sm font-medium text-white">{{ $pkl->student->name ?? '-' }}</p>
                                                <p class="text-xs text-slate-400">{{ $pkl->student->nis ?? '-' }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2.5 py-1 rounded-lg bg-slate-800 border border-slate-700 text-xs font-medium text-slate-300">
                                                {{ $pkl->student->kelas->nm_kls ?? '-' }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="text-sm font-bold {{ $finalGrade >= 70 ? 'text-emerald-400' : 'text-red-400' }}">
                                                {{ number_format($finalGrade, 1) }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium border {{ $predikatColor }}">
                                                {{ $predikat }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <div class="relative inline-block" x-data="{ open: false }">
                                                <button @click="open = !open" 
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 rounded-lg text-xs font-medium transition-colors border border-amber-500/20 cursor-pointer">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                                                    </svg>
                                                    Cetak
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                                    </svg>
                                                </button>
                                                <div x-show="open" @click.away="open = false" 
                                                    class="absolute right-0 mt-1 w-36 rounded-lg shadow-2xl z-50"
                                                    style="background-color: #1e293b;"
                                                    x-transition:enter="transition ease-out duration-100"
                                                    x-transition:enter-start="transform opacity-0 scale-95"
                                                    x-transition:enter-end="transform opacity-100 scale-100"
                                                    x-transition:leave="transition ease-in duration-75"
                                                    x-transition:leave-start="transform opacity-100 scale-100"
                                                    x-transition:leave-end="transform opacity-0 scale-95">
                                                    <div class="py-1 border border-slate-600 rounded-lg overflow-hidden">
                                                        <a href="{{ route('admin.pkl.suket.print', $pkl->id) }}?paper_size=a4" target="_blank"
                                                            class="flex items-center gap-2 px-4 py-2 text-xs text-slate-200 hover:bg-slate-700 hover:text-white transition-colors">
                                                            <span>📄</span> A4
                                                        </a>
                                                        <a href="{{ route('admin.pkl.suket.print', $pkl->id) }}?paper_size=legal" target="_blank"
                                                            class="flex items-center gap-2 px-4 py-2 text-xs text-slate-200 hover:bg-slate-700 hover:text-white transition-colors">
                                                            <span>📄</span> Legal
                                                        </a>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @empty
                <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-12 text-center">
                    <div class="flex flex-col items-center justify-center">
                        <div class="w-16 h-16 rounded-full bg-slate-800/50 flex items-center justify-center mb-4">
                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-medium text-white mb-1">Belum ada data</h3>
                        <p class="text-slate-400">Belum ada siswa PKL yang memiliki nilai lengkap.</p>
                    </div>
                </div>
            @endforelse

            @if($pklList instanceof \Illuminate\Pagination\LengthAwarePaginator)
                <div class="mt-6">
                    {{ $pklList->appends(request()->all())->links() }}
                </div>
            @endif
        </div>

        <!-- Config Modal -->
        <div x-show="showConfigModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showConfigModal = false" style="display: none;">

            <div x-show="showConfigModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-sky-900/95 to-blue-900/95 border border-sky-500/30 rounded-2xl shadow-2xl shadow-blue-500/20"
                x-data="{
                        bgFrontPreview: '{{ $bgFront ? asset('storage/' . $bgFront) : '' }}',
                        bgBackPreview: '{{ $bgBack ? asset('storage/' . $bgBack) : '' }}',
                        handleFileChange(event, type) {
                            const file = event.target.files[0];
                            if (file) {
                                const reader = new FileReader();
                                reader.onload = (e) => {
                                    if (type === 'front') {
                                        this.bgFrontPreview = e.target.result;
                                    } else {
                                        this.bgBackPreview = e.target.result;
                                    }
                                };
                                reader.readAsDataURL(file);
                            }
                        }
                    }">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-sky-500/30 bg-sky-800/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-white">Konfigurasi Sertifikat PKL</h3>
                        <p class="text-sm text-sky-300/70 mt-0.5">Atur background, tanggal PKL, dan tahun pelajaran</p>
                    </div>
                    <button @click="showConfigModal = false" class="text-sky-300 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.pkl.suket.config') }}" method="POST" enctype="multipart/form-data"
                    class="p-6 space-y-6">
                    @csrf

                    <!-- Background Section -->
                    <div class="border border-sky-500/30 rounded-xl overflow-hidden bg-sky-950/30">
                        <div class="bg-sky-800/30 px-4 py-3 border-b border-sky-500/30">
                            <span class="text-sm font-medium text-sky-200">Background Sertifikat</span>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Front Background -->
                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-sky-200">Background Depan</label>
                                    <div class="flex items-center gap-4">
                                        <div class="relative w-32 h-20 bg-slate-800/50 rounded-lg border border-slate-700/50 overflow-hidden flex-shrink-0 cursor-pointer hover:border-sky-500 transition-colors"
                                            @click="$refs.frontInput.click()">
                                            <img x-show="bgFrontPreview" :src="bgFrontPreview"
                                                class="w-full h-full object-contain">
                                            <div x-show="!bgFrontPreview"
                                                class="absolute inset-0 flex items-center justify-center text-slate-500">
                                                <svg class="w-6 h-6 opacity-50" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <button type="button" @click="$refs.frontInput.click()"
                                                class="px-4 py-2 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-lg text-sm font-medium hover:bg-sky-500/30 transition-colors">
                                                <span x-text="bgFrontPreview ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
                                            </button>
                                            <p class="text-xs text-slate-400 mt-2">JPG, PNG (Max 2MB)</p>
                                        </div>
                                        <input type="file" name="suket_bg_front" accept="image/*" x-ref="frontInput"
                                            @change="handleFileChange($event, 'front')" class="hidden">
                                    </div>
                                </div>

                                <!-- Back Background -->
                                <div class="space-y-3">
                                    <label class="block text-sm font-medium text-sky-200">Background Belakang</label>
                                    <div class="flex items-center gap-4">
                                        <div class="relative w-32 h-20 bg-slate-800/50 rounded-lg border border-slate-700/50 overflow-hidden flex-shrink-0 cursor-pointer hover:border-sky-500 transition-colors"
                                            @click="$refs.backInput.click()">
                                            <img x-show="bgBackPreview" :src="bgBackPreview"
                                                class="w-full h-full object-contain">
                                            <div x-show="!bgBackPreview"
                                                class="absolute inset-0 flex items-center justify-center text-slate-500">
                                                <svg class="w-6 h-6 opacity-50" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                        d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                                </svg>
                                            </div>
                                        </div>
                                        <div class="flex-1">
                                            <button type="button" @click="$refs.backInput.click()"
                                                class="px-4 py-2 bg-sky-500/20 text-sky-300 border border-sky-500/30 rounded-lg text-sm font-medium hover:bg-sky-500/30 transition-colors">
                                                <span x-text="bgBackPreview ? 'Ganti Gambar' : 'Pilih Gambar'"></span>
                                            </button>
                                            <p class="text-xs text-slate-400 mt-2">JPG, PNG (Max 2MB)</p>
                                        </div>
                                        <input type="file" name="suket_bg_back" accept="image/*" x-ref="backInput"
                                            @change="handleFileChange($event, 'back')" class="hidden">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Date Configuration Section -->
                    <div class="border border-sky-500/30 rounded-xl overflow-hidden bg-sky-950/30">
                        <div class="bg-sky-800/30 px-4 py-3 border-b border-sky-500/30">
                            <span class="text-sm font-medium text-sky-200">Konfigurasi Tanggal</span>
                        </div>
                        <div class="p-4">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Tahun Pelajaran -->
                                <div>
                                    <label class="block text-sm font-medium text-sky-200 mb-2">Tahun Pelajaran</label>
                                    <select name="m_tp_id"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                        <option value="">-- Pilih Tahun Pelajaran --</option>
                                        @foreach ($tpList as $tp)
                                            <option value="{{ $tp->id }}"
                                                {{ ($sertifikat && $sertifikat->m_tp_id ? $sertifikat->m_tp_id : $tpId) == $tp->id ? 'selected' : '' }}>
                                                {{ $tp->nm_tp }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <!-- Tanggal Cetak -->
                                <div>
                                    <label class="block text-sm font-medium text-sky-200 mb-2">Tanggal Cetak</label>
                                    <input type="date" name="tgl_cetak"
                                        value="{{ $sertifikat && $sertifikat->tgl_cetak ? $sertifikat->tgl_cetak->format('Y-m-d') : '' }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                </div>

                                <!-- Tanggal Mulai -->
                                <div>
                                    <label class="block text-sm font-medium text-sky-200 mb-2">Tanggal Mulai PKL</label>
                                    <input type="date" name="tgl_mulai"
                                        value="{{ $sertifikat && $sertifikat->tgl_mulai ? $sertifikat->tgl_mulai->format('Y-m-d') : '' }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                </div>

                                <!-- Tanggal Selesai -->
                                <div>
                                    <label class="block text-sm font-medium text-sky-200 mb-2">Tanggal Selesai PKL</label>
                                    <input type="date" name="tgl_selesai"
                                        value="{{ $sertifikat && $sertifikat->tgl_selesai ? $sertifikat->tgl_selesai->format('Y-m-d') : '' }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-end gap-3 pt-5 border-t border-sky-500/30">
                        <button type="button" @click="showConfigModal = false"
                            class="px-5 py-2.5 bg-sky-800/50 border border-sky-500/30 text-white font-medium rounded-xl hover:bg-sky-800 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-sky-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-sky-600 transition-all shadow-lg shadow-blue-500/20 cursor-pointer flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection