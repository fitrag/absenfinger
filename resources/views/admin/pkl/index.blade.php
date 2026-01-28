@extends('layouts.admin')

@section('title', 'Data PKL')

@section('content')
    <div class="space-y-6" x-data="pklPage()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Data PKL</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola data Praktik Kerja Lapangan siswa</p>
            </div>
            <div class="flex items-center gap-3">
                <!-- TP Setting Combobox -->
                <form action="{{ route('admin.pkl.setTp') }}" method="POST" class="flex items-center gap-2">
                    @csrf
                    <label class="text-sm text-slate-400">TP Aktif:</label>
                    <select name="tp_id" onchange="this.form.submit()"
                        class="px-3 py-2 bg-indigo-500/20 border border-indigo-500/30 rounded-lg text-indigo-300 text-sm font-medium cursor-pointer">
                        @foreach($tpList as $tp)
                            <option value="{{ $tp->id }}" {{ $selectedTp && $selectedTp->id == $tp->id ? 'selected' : '' }}
                                class="bg-slate-800 text-white">
                                {{ $tp->nm_tp }}
                            </option>
                        @endforeach
                    </select>
                </form>

                <a href="{{ route('admin.pkl.exportPdf', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-all shadow-lg shadow-slate-900/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                    </svg>
                    <span>PDF</span>
                </a>

                <a href="{{ route('admin.pkl.exportExcel', request()->query()) }}" target="_blank"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-emerald-600 text-white font-medium rounded-xl hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-900/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                    </svg>
                    <span>Excel</span>
                </a>

                <a href="{{ route('admin.pkl.suket.index') }}"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 text-white font-medium rounded-xl hover:bg-indigo-700 transition-all shadow-lg shadow-indigo-900/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                    <span>Konfigurasi Suket</span>
                </a>

                <button @click="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-purple-500/25 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    <span>Tambah PKL</span>
                </button>

                <button @click="showNametagModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-pink-500 to-rose-500 text-white font-medium rounded-xl hover:from-pink-600 hover:to-rose-600 transition-all shadow-lg shadow-pink-500/20 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2" />
                    </svg>
                    <span>Cetak Nametag</span>
                </button>
            </div>
        </div>

        <!-- Stats Card -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['total'] }}</p>
                        <p class="text-xs text-slate-400">Total Data PKL</p>
                    </div>
                </div>
                @if($selectedTp)
                    <span
                        class="inline-flex px-3 py-1.5 text-sm font-medium rounded-lg bg-indigo-500/20 text-indigo-300 border border-indigo-500/30">
                        {{ $selectedTp->nm_tp }}
                    </span>
                @endif
            </div>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-4">
            <form method="GET" action="{{ route('admin.pkl.index') }}" class="flex flex-wrap items-center gap-2">
                <input type="hidden" name="tp_id" value="{{ $tpId }}">
                <select name="kelas_id" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ $kelasId == $kelas->id ? 'selected' : '' }}>{{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>
                <select name="dudi_id" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua DUDI</option>
                    @foreach($dudiList as $dudi)
                        <option value="{{ $dudi->id }}" {{ $dudiId == $dudi->id ? 'selected' : '' }}>{{ $dudi->nama }}</option>
                    @endforeach
                </select>
                <select name="pembimbing_id" onchange="this.form.submit()"
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs">
                    <option value="">Semua Pembimbing</option>
                    @foreach($guruList as $guru)
                        <option value="{{ $guru->id }}" {{ $pembimbingId == $guru->id ? 'selected' : '' }}>{{ $guru->nama }}
                        </option>
                    @endforeach
                </select>
                <input type="text" name="search" value="{{ $search }}" placeholder="Cari..."
                    class="px-3 py-1.5 bg-slate-900/50 border border-slate-700/50 rounded-lg text-white text-xs w-32">
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @php $currentDudiId = null;
                        $groupCounter = 0; @endphp
                        @forelse($pkls as $index => $pkl)
                            @if($currentDudiId !== $pkl->dudi_id)
                                @php $currentDudiId = $pkl->dudi_id;
                                $groupCounter = 0; @endphp
                                <tr class="bg-slate-700/30">
                                    <td colspan="3" class="px-4 py-3">
                                                    <div class="flex items-center justify-between" @click="toggleGroup({{ $pkl->dudi_id }})" class="cursor-pointer">
                                                        <div class="flex items-center gap-2">
                                                            <button type="button" 
                                                                class="p-1 rounded-lg transition-colors duration-200"
                                                                :class="isGroupExpanded({{ $pkl->dudi_id }}) ? 'bg-slate-700 text-slate-300' : 'bg-slate-800 text-slate-400 hover:text-slate-300'">
                                                                <svg class="w-4 h-4 transition-transform duration-200"
                                                                    :class="isGroupExpanded({{ $pkl->dudi_id }}) ? 'rotate-90' : ''"
                                                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                                                                </svg>
                                                            </button>
                                                            <div class="p-1.5 rounded bg-blue-500/10 text-blue-400">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                                                </svg>
                                                            </div>
                                                <div>
                                                    <div class="flex flex-wrap items-center gap-x-6 gap-y-1">
                                                        <h3 class="text-sm font-bold text-white mr-4">{{ $pkl->dudi->nama }}</h3>
                                                        <div class="hidden sm:block w-px h-3 bg-slate-600"></div>
                                                        <div class="flex flex-wrap items-center gap-3 text-xs text-slate-400" style="margin-left:10px">
                                                            <span class="flex items-center gap-1">
                                                                <span class="text-slate-500">Pimpinan:</span> 
                                                                <span class="text-slate-300">{{ $pkl->pimpinan ?? '-' }}</span>
                                                            </span>
                                                            <span class="hidden sm:inline text-slate-600">•</span>
                                                            <span class="flex items-center gap-1">
                                                                <span class="text-slate-500">Pembimbing Industri:</span> 
                                                                <span class="text-slate-300">{{ $pkl->pembimbing_industri ?? '-' }}</span>
                                                            </span>
                                                            <span class="hidden sm:inline text-slate-600">•</span>
                                                            {{-- Status Lokasi --}}
                                                            @if($pkl->dudi->latitude && $pkl->dudi->longitude)
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    Lokasi Diset ({{ $pkl->dudi->radius ?? 100 }}m)
                                                                </span>
                                                                <a href="https://www.google.com/maps?q={{ $pkl->dudi->latitude }},{{ $pkl->dudi->longitude }}" 
                                                                    target="_blank" 
                                                                    class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/20 transition-colors"
                                                                    title="Buka di Google Maps">
                                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                                                    </svg>
                                                                    Lihat Lokasi
                                                                </a>
                                                            @else
                                                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                                                    <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                                                    </svg>
                                                                    Lokasi Belum Diset
                                                                </span>
                                                            @endif
                                                        </div>
                                                    </div>
                                                    <div class="flex flex-col gap-0.5 mt-1">
                                                        <p class="text-xs text-slate-400">{{ $pkl->dudi->alamat ?? '-' }}</p>
                                                        <p class="text-xs text-slate-400 flex items-center gap-1">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                                            </svg>
                                                            Pembimbing Sekolah: {{ $pkl->pembimbingSekolah->nama ?? 'Belum ada pembimbing' }}
                                                        </p>
                                                    </div>
                                                </div>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <button @click="openSupervisorModal({
                                                    id: {{ $pkl->dudi->id }},
                                                    nama: '{{ addslashes($pkl->dudi->nama) }}',
                                                    pimpinan: '{{ addslashes($pkl->pimpinan ?? '') }}',
                                                    pembimbing_industri: '{{ addslashes($pkl->pembimbing_industri ?? '') }}'
                                                })"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 text-amber-400 hover:bg-amber-500/20 text-xs font-medium rounded-lg border border-amber-500/20 transition-colors"
                                                    title="Edit Data Pimpinan & Pembimbing Industri">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                    </svg>
                                                    <span>Edit Pembimbing</span>
                                                </button>
                                                <button @click="openAddModal({{ $pkl->dudi->id }}, {{ $pkl->pembimbing_sekolah_id ?? 'null' }})"
                                                    class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-300 hover:bg-blue-500/30 text-xs font-medium rounded-lg border border-blue-500/30 transition-colors">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M12 4v16m8-8H4" />
                                                    </svg>
                                                    <span>Tambah Siswa</span>
                                                </button>
                                            </div>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                            @php $groupCounter++; @endphp
                            @php $groupCounter++; @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors" x-show="isGroupExpanded({{ $pkl->dudi_id }})"
                                x-transition:enter="transition ease-out duration-200"
                                x-transition:enter-start="opacity-0 -translate-y-2"
                                x-transition:enter-end="opacity-100 translate-y-0">
                                <td
                                    class="px-4 py-1.5 text-sm text-slate-400 pl-8 border-l-4 border-transparent hover:border-slate-700/50">
                                    {{ $groupCounter }}
                                </td>
                                <td class="px-4 py-1.5">
                                    <p class="text-sm font-medium text-white">{{ $pkl->student->name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400">{{ $pkl->student->nis ?? '-' }} •
                                        {{ $pkl->student->kelas->nm_kls ?? '-' }}
                                    </p>
                                </td>
                                <td class="px-4 py-1.5 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        {{-- <button @click="openEditModal({{ json_encode($pkl) }})" title="Edit"
                                            class="p-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/40 rounded-lg transition-colors cursor-pointer border border-blue-500/30">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button> --}}
                                        <form action="{{ route('admin.pkl.destroy', $pkl->id) }}" method="POST" class="inline"
                                            onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" title="Hapus"
                                                class="p-1.5 bg-red-500/20 text-red-400 hover:bg-red-500/40 rounded-lg transition-colors cursor-pointer border border-red-500/30">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data PKL</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pkls->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $pkls->links() }}
                </div>
            @endif
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showAddModal = false" style="display: none;">

            <div x-show="showAddModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-sky-900/95 to-blue-900/95 border border-sky-500/30 rounded-2xl shadow-2xl shadow-blue-500/20">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-sky-500/30 bg-sky-800/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Data PKL</h3>
                        <p class="text-sm text-sky-300/70 mt-0.5">TP: {{ $selectedTp?->nm_tp ?? '-' }}</p>
                    </div>
                    <button @click="showAddModal = false" class="text-sky-300 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.pkl.store') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    <input type="hidden" name="tp_id" value="{{ $tpId }}">

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Tempat PKL (DUDI) <span
                                    class="text-red-400">*</span></label>
                            <select name="dudi_id" x-model="addForm.dudi_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih DUDI</option>
                                @foreach($dudiList as $dudi)
                                    <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Pembimbing Sekolah</label>
                            <select name="pembimbing_sekolah_id" x-model="addForm.pembimbing_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Guru</option>
                                @foreach($guruList as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Pimpinan (Opsional)</label>
                            <input type="text" name="pimpinan" x-model="addForm.pimpinan" placeholder="Nama Pimpinan DUDI"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Pembimbing Industri (Opsional)</label>
                            <input type="text" name="pembimbing_industri" x-model="addForm.pembimbing_industri" placeholder="Nama Pembimbing di DUDI"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500">
                        </div>
                    </div>

                    <!-- Student Selection -->
                    <div class="border border-sky-500/30 rounded-xl overflow-hidden bg-sky-950/30">
                        <div class="bg-sky-800/30 px-4 py-3 border-b border-sky-500/30">
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <span class="text-sm font-medium text-sky-200">Pilih Siswa <span
                                        class="text-red-400">*</span></span>
                                <div class="flex items-center gap-2">
                                    <select x-model="selectedKelas" @change="loadStudents()"
                                        class="px-3 py-1.5 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm">
                                        <option value="">Pilih Kelas</option>
                                        @foreach($kelasPklList as $kelas)
                                            <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                        @endforeach
                                    </select>
                                    <button type="button" @click="selectAll()"
                                        class="text-xs px-3 py-1.5 bg-emerald-500/20 text-emerald-300 rounded-lg hover:bg-emerald-500/30 transition-colors border border-emerald-500/30">
                                        Pilih Semua
                                    </button>
                                    <button type="button" @click="deselectAll()"
                                        class="text-xs px-3 py-1.5 bg-slate-500/20 text-slate-300 rounded-lg hover:bg-slate-500/30 transition-colors border border-slate-500/30">
                                        Hapus Pilihan
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="max-h-60 overflow-y-auto p-4">
                            <template x-if="isLoading">
                                <div class="text-center text-sky-300 text-sm py-4">
                                    <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-sky-400" fill="none"
                                        viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                            stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Memuat data...
                                </div>
                            </template>
                            <template x-if="!isLoading && students.length === 0 && selectedKelas">
                                <div class="text-center text-sky-300 text-sm py-4">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Semua siswa di kelas ini sudah terdaftar PKL
                                </div>
                            </template>
                            <template x-if="!isLoading && !selectedKelas">
                                <div class="text-center text-slate-400 text-sm py-4">
                                    Pilih kelas untuk menampilkan siswa
                                </div>
                            </template>
                            <div x-show="!isLoading && students.length > 0" class="grid grid-cols-1 md:grid-cols-2 gap-2">
                                <template x-for="student in students" :key="student.id">
                                    <label
                                        class="flex items-center gap-3 p-3 rounded-lg bg-sky-800/20 hover:bg-sky-800/40 cursor-pointer transition-colors border border-sky-500/20">
                                        <input type="checkbox" name="student_ids[]" :value="student.id"
                                            x-model="selectedStudents"
                                            class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500">
                                        <div>
                                            <p class="text-sm text-white font-medium" x-text="student.name"></p>
                                            <p class="text-xs text-slate-400" x-text="student.nis"></p>
                                        </div>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-5 border-t border-sky-500/30">
                        <span class="text-sm text-sky-300" x-text="selectedStudents.length + ' siswa dipilih'"></span>
                        <div class="flex gap-3">
                            <button type="button" @click="showAddModal = false"
                                class="px-5 py-2.5 bg-sky-800/50 border border-sky-500/30 text-white font-medium rounded-xl hover:bg-sky-800 transition-colors cursor-pointer">
                                Batal
                            </button>
                            <button type="submit" :disabled="selectedStudents.length === 0"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-sky-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-sky-600 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none cursor-pointer">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showEditModal = false" style="display: none;">

            <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-sky-900/95 to-blue-900/95 border border-sky-500/30 rounded-2xl shadow-2xl shadow-blue-500/20">

                <div class="flex items-center justify-between px-6 py-4 border-b border-sky-500/30 bg-sky-800/30 rounded-t-2xl">
                    <h3 class="text-lg font-bold text-white">Edit Data PKL</h3>
                    <button @click="showEditModal = false" class="text-sky-300 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="`/admin/pkl/${editPkl.id}`" method="POST" class="p-6 space-y-4">
                    @csrf
                    @method('PUT')
                    <input type="hidden" name="student_id" :value="editPkl.student_id">
                    <input type="hidden" name="tp_id" :value="editPkl.tp_id">

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Siswa</label>
                        <input type="text" :value="editPkl.student ? editPkl.student.name : '-'" disabled
                            class="w-full px-4 py-2.5 bg-slate-700/50 border border-slate-700/50 rounded-xl text-slate-400 text-sm">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tempat PKL (DUDI) <span
                                class="text-red-400">*</span></label>
                        <select name="dudi_id" x-model="editPkl.dudi_id" required
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="">Pilih DUDI</option>
                            @foreach($dudiList as $dudi)
                                <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pembimbing Sekolah</label>
                        <select name="pembimbing_sekolah_id" x-model="editPkl.pembimbing_sekolah_id"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="">Pilih Guru</option>
                            @foreach($guruList as $guru)
                                <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pimpinan (Opsional)</label>
                        <input type="text" name="pimpinan" x-model="editPkl.pimpinan" placeholder="Nama Pimpinan DUDI"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Pembimbing Industri (Opsional)</label>
                        <input type="text" name="pembimbing_industri" x-model="editPkl.pembimbing_industri" placeholder="Nama Pembimbing di DUDI"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500">
                    </div>

                    <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    <!-- Supervisor Edit Modal -->
    <div x-show="showSupervisorModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
        @click.self="showSupervisorModal = false" style="display: none;">

        <div x-show="showSupervisorModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-lg bg-gradient-to-br from-slate-900/95 to-slate-800/95 border border-slate-700/50 rounded-2xl shadow-2xl shadow-slate-900/50">

            <div
                class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50 bg-slate-800/50 rounded-t-2xl">
                <div>
                    <h3 class="text-lg font-bold text-white">Edit Pembimbing DUDI</h3>
                    <p class="text-sm text-slate-400 mt-0.5" x-text="supervisorForm.dudi_nama"></p>
                </div>
                <button @click="showSupervisorModal = false" class="text-slate-400 hover:text-white transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pkl.updateSupervisors') }}" method="POST" class="p-6 space-y-4">
                @csrf
                <input type="hidden" name="tp_id" value="{{ $tpId }}">
                <input type="hidden" name="dudi_id" x-model="supervisorForm.dudi_id">

                <div class="bg-amber-500/10 border border-amber-500/20 rounded-lg p-3">
                    <p class="text-xs text-amber-400 flex items-start gap-2">
                        <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Perubahan ini akan diterapkan ke semua siswa PKL aktif di <span class="font-bold"
                                x-text="supervisorForm.dudi_nama"></span> pada TP ini.</span>
                    </p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pimpinan DUDI (Opsional)</label>
                    <input type="text" name="pimpinan" x-model="supervisorForm.pimpinan" placeholder="Nama Pimpinan"
                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all">
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pembimbing Industri (Opsional)</label>
                    <input type="text" name="pembimbing_industri" x-model="supervisorForm.pembimbing_industri"
                        placeholder="Nama Pembimbing Lapangan"
                        class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm placeholder-slate-500 focus:ring-2 focus:ring-indigo-500/50 focus:border-indigo-500/50 transition-all">
                </div>

                <div class="flex justify-end gap-3 pt-4 border-t border-slate-700/50">
                    <button type="button" @click="showSupervisorModal = false"
                        class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer text-sm font-medium">Batal</button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all cursor-pointer text-sm shadow-lg shadow-amber-500/20">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <!-- Nametag Print Modal -->
    <div x-show="showNametagModal" x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-10 bg-black/50 backdrop-blur-sm overflow-y-auto"
        @click.self="showNametagModal = false" style="display: none;">

        <div x-show="showNametagModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            class="w-full max-w-md bg-gradient-to-br from-slate-900/95 to-slate-800/95 border border-slate-700/50 rounded-2xl shadow-2xl shadow-slate-900/50">

            <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50 bg-slate-800/50 rounded-t-2xl">
                <h3 class="text-lg font-bold text-white">Cetak Nametag Peserta PKL</h3>
                <button @click="showNametagModal = false" class="text-slate-400 hover:text-white transition-colors p-1">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.pkl.printNametag') }}" method="GET" target="_blank" class="p-6 space-y-6">
                <input type="hidden" name="tp_id" value="{{ $tpId }}">

                <div>
                    <label class="block text-sm font-medium text-slate-300 mb-2">Pilih DUDI</label>
                    <select name="dudi_id" class="w-full px-4 py-2.5 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm focus:ring-2 focus:ring-pink-500/50 focus:border-pink-500/50 transition-all">
                        <option value="all">Semua DUDI</option>
                        @foreach($dudiList as $dudi)
                            <option value="{{ $dudi->id }}">{{ $dudi->nama }}</option>
                        @endforeach
                    </select>
                    <p class="mt-2 text-xs text-slate-400">Pilih "Semua DUDI" untuk mencetak semua siswa PKL aktif pada TP ini.</p>
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button type="button" @click="showNametagModal = false"
                        class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer text-sm font-medium">Batal</button>
                    <button type="submit"
                        class="px-5 py-2.5 bg-gradient-to-r from-pink-600 to-rose-600 text-white font-medium rounded-xl hover:from-pink-700 hover:to-rose-700 transition-all cursor-pointer text-sm shadow-lg shadow-pink-900/20 flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak PDF
                    </button>
                </div>
            </form>
        </div>
    </div>
    </div>

    <script>
        function pklPage() {
            return {
                showAddModal: false,
                showEditModal: false,
                showNametagModal: false,
                addForm: {
                    dudi_id: '',
                    pembimbing_id: '',
                    pembimbing_industri: '',
                    pimpinan: ''
                },
                selectedKelas: '',
                selectedTp: '{{ $tpId ?? '' }}',
                students: [],
                selectedStudents: [],
                isLoading: false,
                editPkl: {},
                showSupervisorModal: false,
                supervisorForm: {
                    dudi_id: '',
                    dudi_nama: '',
                    pimpinan: '',
                    pembimbing_industri: ''
                },

                supervisorForm: {
                    dudi_id: '',
                    dudi_nama: '',
                    pimpinan: '',
                    pembimbing_industri: ''
                },
                expandedGroups: {}, // Store expansion state: { id: boolean }

                toggleGroup(id) {
                    if (this.expandedGroups[id] === undefined) {
                        this.expandedGroups[id] = false; // Toggle to false (hide) if undefined (default shown is visually simpler but tracking state needs initialization. Let's assume default is OPEN so we toggle to close?)
                        // Actually, better: if undefined, set to FALSE?
                        // Let's make default OPEN.
                        // So if undefined, it's open.
                        // this.expandedGroups[id] = false;
                    } 
                    // Let's stick to: undefined = hidden? Or undefined = visible?
                    // User request: "buatkan tampil dan hide". Usually starts expanded or user clicks to expand.
                    // Given the list might be long, maybe default expanded is better?
                    // Let's implement: Default Expanded.
                    if (this.expandedGroups[id] === undefined) {
                         this.expandedGroups[id] = false; // undefined means OPEN (true implied), so clicking toggles to CLOSE (false).
                    } else {
                         this.expandedGroups[id] = !this.expandedGroups[id];
                    }
                },

                isGroupExpanded(id) {
                    // Default to true (expanded)
                    return this.expandedGroups[id] === undefined ? true : this.expandedGroups[id];
                },

                init() {
                    this.$watch('showAddModal', value => {
                        document.body.classList.toggle('overflow-hidden', value);
                        if (!value) {
                            this.selectedKelas = '';
                            this.students = [];
                            this.selectedStudents = [];
                            this.addForm.dudi_id = '';
                            this.addForm.pembimbing_id = '';
                            this.addForm.pembimbing_industri = '';
                            this.addForm.pimpinan = '';
                        }
                    });
                    this.$watch('showEditModal', value => {
                        document.body.classList.toggle('overflow-hidden', value);
                    });
                    this.$watch('showSupervisorModal', value => {
                        document.body.classList.toggle('overflow-hidden', value);
                    });
                    this.$watch('showNametagModal', value => {
                        document.body.classList.toggle('overflow-hidden', value);
                    });
                },

                loadStudents() {
                    if (!this.selectedKelas) {
                        this.students = [];
                        return;
                    }
                    this.isLoading = true;
                    this.students = [];
                    this.selectedStudents = [];

                    fetch(`/admin/pkl/students/${this.selectedKelas}?tp_id=${this.selectedTp}`)
                        .then(res => res.json())
                        .then(data => {
                            this.students = data;
                            this.isLoading = false;
                        })
                        .catch(() => {
                            this.isLoading = false;
                        });
                },

                selectAll() {
                    this.selectedStudents = this.students.map(s => s.id.toString());
                },

                deselectAll() {
                    this.selectedStudents = [];
                },

                openAddModal(dudiId = '', pembimbingId = '') {
                    this.addForm.dudi_id = dudiId;
                    this.addForm.pembimbing_id = pembimbingId ? pembimbingId.toString() : '';
                    this.showAddModal = true;
                },

                openEditModal(pkl) {
                    this.editPkl = { ...pkl };
                    this.showEditModal = true;
                },

                openSupervisorModal(data) {
                    this.supervisorForm = {
                        dudi_id: data.id,
                        dudi_nama: data.nama,
                        pimpinan: data.pimpinan,
                        pembimbing_industri: data.pembimbing_industri
                    };
                    this.showSupervisorModal = true;
                }
            }
        }
    </script>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-red-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection