@extends('layouts.admin')

@section('title', 'Data Presensi')
@section('page-title', 'Data Presensi')

@section('content')
    <div class="space-y-6" x-data="attendancePage()">
        <!-- Header & Stats -->
        <div class="space-y-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Presensi</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Rekap kehadiran siswa tanggal {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                    @if(isset($isWaliKelas) && $isWaliKelas && isset($walasKelasInfo) && !$isAdmin)
                        <span class="ml-2 px-2 py-0.5 bg-blue-500/20 text-blue-400 rounded-lg text-xs">
                            Kelas {{ $walasKelasInfo->nm_kls }}
                        </span>
                    @endif
                </p>
            </div>
            {{-- Tombol tampil jika: Admin, Piket, atau Piket+Walas. Tidak tampil jika: Kepsek atau Walas saja --}}
            @if(!$isKepsek && (($isAdmin && !in_array('Kepsek', session('user_roles', []))) || $isPiket))
                <div class="flex flex-wrap gap-2" style="justify-content: flex-end;">
                    <button @click="showAbsenceModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all shadow-lg shadow-blue-500/20 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                        </svg>
                        Input Ketidakhadiran
                    </button>
                    <button @click="showUpdateModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                        </svg>
                        Update Ketidakhadiran
                    </button>
                    <button @click="showExportModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        Export Excel
                    </button>
                    <button @click="showPrintStatusModal = true"
                        class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-rose-500 to-pink-500 text-white font-medium rounded-xl hover:from-rose-600 hover:to-pink-600 transition-all shadow-lg shadow-rose-500/20 cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        Cetak Status
                    </button>
                </div>
            @endif
        </div>

        <!-- Compact Stats -->
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="text-slate-400">Rekap:</span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20">
                <span class="font-bold">{{ $totalStudents }}</span> Total
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/20">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span class="font-bold">{{ $checkinCount }}</span> Checkin
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                <span class="font-bold">{{ $hadirCount }}</span> Hadir
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-purple-500/10 text-purple-400 border border-purple-500/20">
                <span class="font-bold">{{ $sakitCount }}</span> Sakit
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                <span class="font-bold">{{ $izinCount }}</span> Izin
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-slate-500/10 text-slate-400 border border-slate-500/20">
                <span class="font-bold">{{ $alphaCount }}</span> Alpha
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-orange-500/10 text-orange-400 border border-orange-500/20">
                <span class="font-bold">{{ $belumAbsenCount }}</span> Belum Absen
            </span>
        </div>

        <!-- Filters -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-3">
            <form action="{{ route('admin.attendance.index') }}" method="GET" class="flex flex-wrap items-center gap-3">
                <!-- Date -->
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 date-white-icon">

                <!-- Submit -->
                <button type="submit"
                    class="px-4 py-2 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-colors text-sm cursor-pointer">
                    Filter
                </button>
                <a href="{{ route('admin.attendance.index') }}"
                    class="px-4 py-2 bg-slate-700 text-white font-medium rounded-lg hover:bg-slate-600 transition-colors text-sm">
                    Reset
                </a>
            </form>
        </div>
        <div class="flex items-center gap-2 text-xs">
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-teal-500/10 text-teal-400 border border-teal-500/20">
                <span class="font-bold">M</span> <span class="text-slate-400">→</span> Jumlah Masuk
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                <span class="font-bold">P</span> <span class="text-slate-400">→</span>Jumlah Pulang
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-green-500/10 text-green-400 border border-green-500/20">
                <span class="font-bold">H</span> <span class="text-slate-400">→</span>Jumlah Hadir
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-red-500/10 text-red-400 border border-red-500/20">
                <span class="font-bold">BA</span> <span class="text-slate-400">→</span>Jumlah Belum Absen
            </span>
            <span
                class="inline-flex items-center gap-1 px-2 py-1 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20">
                <span class="font-bold">B</span> <span class="text-slate-400">→</span>Jumlah Bolos
            </span>
        </div>
        <!-- Table Per Kelas -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50">
            <div class="overflow-x-auto">
                <table class="w-full attendance-table">
                    <thead class="bg-slate-900">
                        <tr class="border-b border-slate-800/50">
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                No</th>
                            <th
                                class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                Kelas</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-teal-400 uppercase tracking-wider bg-slate-900"
                                title="Sudah Check-in">
                                M
                            </th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-cyan-400 uppercase tracking-wider bg-slate-900"
                                title="Sudah Check-out">
                                P
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-emerald-400 uppercase tracking-wider bg-slate-900">
                                H</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-purple-400 uppercase tracking-wider bg-slate-900">
                                <Samp>S</Samp>
                            </th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-blue-400 uppercase tracking-wider bg-slate-900">
                                I</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-red-400 uppercase tracking-wider bg-slate-900">
                                A</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-rose-400 uppercase tracking-wider bg-slate-900"
                                title="Bolos (Masuk tanpa Pulang)">
                                B</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-500 uppercase tracking-wider bg-slate-900">
                                BA</th>
                            <th
                                class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider bg-slate-900">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($kelasAttendance as $index => $data)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="text-sm font-medium text-white">{{ $data['nama_kelas'] }}</div>
                                    <div class="text-xs text-slate-400">{{ $data['jumlah_siswa'] }} siswa</div>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['checkin'] > 0 ? 'bg-teal-500/10 text-teal-400 border border-teal-500/20' : 'text-slate-500' }}">
                                        {{ $data['checkin'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['checkout'] > 0 ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'text-slate-500' }}">
                                        {{ $data['checkout'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['hadir'] > 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'text-slate-500' }}">
                                        {{ $data['hadir'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['sakit'] > 0 ? 'bg-purple-500/10 text-purple-400 border border-purple-500/20' : 'text-slate-500' }}">
                                        {{ $data['sakit'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['izin'] > 0 ? 'bg-blue-500/10 text-blue-400 border border-blue-500/20' : 'text-slate-500' }}">
                                        {{ $data['izin'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['alpha'] > 0 ? 'bg-red-500/10 text-red-400 border border-red-500/20' : 'text-slate-500' }}">
                                        {{ $data['alpha'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['bolos'] > 0 ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'text-slate-500' }}">
                                        {{ $data['bolos'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium {{ $data['tidak_absen'] > 0 ? 'bg-slate-500/10 text-slate-400 border border-slate-500/20' : 'text-slate-500' }}">
                                        {{ $data['tidak_absen'] }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <a href="{{ route('admin.attendance.showByKelas', ['kelasId' => $data['id'], 'date' => $date]) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 rounded-lg text-xs font-medium transition-colors border border-blue-500/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                        </svg>

                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data kelas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                    <!-- Total Row -->
                    <tfoot class="border-t border-slate-700/50 bg-slate-800/30">
                        <tr>
                            <td class="px-4 py-3 text-sm font-semibold text-white" colspan="2">Total ({{ $totalStudents }}
                                siswa)</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-teal-400">{{ $checkinCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-cyan-400">{{ $checkoutCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-emerald-400">{{ $hadirCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-purple-400">{{ $sakitCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-blue-400">{{ $izinCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-red-400">{{ $alphaCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-rose-400">{{ $bolosCount }}</td>
                            <td class="px-4 py-3 text-center text-sm font-semibold text-slate-400">{{ $belumAbsenCount }}
                            </td>
                            <td class="px-4 py-3"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Absence Modal -->
        <div x-cloak x-show="showAbsenceModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showAbsenceModal = false">

            <div x-show="showAbsenceModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-sky-900/95 to-blue-900/95 border border-sky-500/30 rounded-2xl shadow-2xl shadow-blue-500/20">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-sky-500/30 bg-sky-800/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-white">Input Ketidakhadiran</h3>
                        <p class="text-sm text-sky-300/70 mt-0.5">Pilih tanggal dan kelas untuk menampilkan siswa</p>
                    </div>
                    <button @click="showAbsenceModal = false" class="text-sky-300 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.attendance.store-absence') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Tanggal</label>
                            <input type="date" name="date" x-model="absenceDate"
                                x-init="absenceDate = new Date().toISOString().split('T')[0]" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 date-white-icon"
                                @change="loadStudents(kelasId)">
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-sm font-medium text-sky-200 mb-2">Kelas</label>
                            <select x-model="kelasId"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div x-show="kelasId" x-cloak class="border border-sky-500/30 rounded-xl overflow-hidden bg-sky-950/30">
                        <div class="bg-sky-800/30 px-4 py-3 border-b border-sky-500/30">
                            <div class="flex flex-wrap justify-between items-center gap-2">
                                <span class="text-sm font-medium text-sky-200">Daftar Siswa (belum absen)</span>
                                <div class="flex flex-wrap gap-2">
                                    <button type="button" @click="setAllStatus('sakit')"
                                        class="text-xs px-3 py-1.5 bg-purple-500/20 text-purple-300 rounded-lg hover:bg-purple-500/30 transition-colors border border-purple-500/30 cursor-pointer">Semua
                                        Sakit</button>
                                    <button type="button" @click="setAllStatus('izin')"
                                        class="text-xs px-3 py-1.5 bg-cyan-500/20 text-cyan-300 rounded-lg hover:bg-cyan-500/30 transition-colors border border-cyan-500/30 cursor-pointer">Semua
                                        Izin</button>
                                    <button type="button" @click="setAllStatus('alpha')"
                                        class="text-xs px-3 py-1.5 bg-red-500/20 text-red-300 rounded-lg hover:bg-red-500/30 transition-colors border border-red-500/30 cursor-pointer">Semua
                                        Alpha</button>
                                </div>
                            </div>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            <template x-if="isLoading">
                                <div class="p-6 text-center text-sky-300 text-sm">
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
                            <template x-if="!isLoading && students.length === 0">
                                <div class="p-6 text-center text-sky-300 text-sm">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-emerald-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    Semua siswa sudah absen hari ini
                                </div>
                            </template>
                            <table x-show="!isLoading && students.length > 0" class="w-full">
                                <thead class="bg-sky-800/40 sticky top-0">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-sky-200 uppercase tracking-wider">
                                            Siswa</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-sky-200 uppercase tracking-wider">
                                            NIS</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-sky-200 uppercase tracking-wider">
                                            Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-sky-500/20">
                                    <template x-for="(student, index) in students" :key="student.id">
                                        <tr class="hover:bg-sky-800/20">
                                            <td class="px-4 py-3">
                                                <span class="text-sm text-white font-medium" x-text="student.name"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs text-slate-400 font-mono" x-text="student.nis"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <input type="hidden" :name="'students['+index+'][nis]'"
                                                    :value="student.nis">
                                                <select :name="'students['+index+'][status]'" x-model="student.status"
                                                    class="text-sm px-3 py-1.5 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50">
                                                    <option value="">-- Pilih --</option>
                                                    <option value="sakit">Sakit</option>
                                                    <option value="izin">Izin</option>
                                                    <option value="alpha">Alpha</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-5 border-t border-sky-500/30">
                        <span class="text-sm text-sky-300" x-text="getSelectedCount() + ' siswa dipilih'"></span>
                        <div class="flex gap-3">
                            <button type="button" @click="showAbsenceModal = false"
                                class="px-5 py-2.5 bg-sky-800/50 border border-sky-500/30 text-white font-medium rounded-xl hover:bg-sky-800 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="getSelectedCount() === 0"
                                class="px-5 py-2.5 bg-gradient-to-r from-blue-500 to-sky-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-sky-600 transition-all shadow-lg shadow-blue-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                                Simpan
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Update Absence Modal -->
        <div x-cloak x-show="showUpdateModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-16 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showUpdateModal = false">

            <div x-show="showUpdateModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-4xl bg-gradient-to-br from-amber-900/95 to-orange-900/95 border border-amber-500/30 rounded-2xl shadow-2xl shadow-amber-500/20">

                <div
                    class="flex items-center justify-between px-6 py-4 border-b border-amber-500/30 bg-amber-800/30 rounded-t-2xl">
                    <div>
                        <h3 class="text-lg font-bold text-white">Update Ketidakhadiran</h3>
                        <p class="text-sm text-amber-300/70 mt-0.5">Edit status siswa yang sudah tercatat (Sakit/Izin/Alpha)
                        </p>
                    </div>
                    <button @click="showUpdateModal = false" class="text-amber-300 hover:text-white transition-colors p-1">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.attendance.update-absence') }}" method="POST" class="p-6 space-y-6">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Date -->
                        <div>
                            <label class="block text-sm font-medium text-amber-200 mb-2">Tanggal</label>
                            <input type="date" name="date" x-model="updateDate" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 date-white-icon"
                                @change="loadAbsentStudents()">
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-sm font-medium text-amber-200 mb-2">Kelas</label>
                            <select x-model="updateKelasId"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <!-- Students List -->
                    <div x-show="updateKelasId" x-cloak
                        class="border border-amber-500/30 rounded-xl overflow-hidden bg-amber-950/30">
                        <div class="bg-amber-800/30 px-4 py-3 border-b border-amber-500/30">
                            <span class="text-sm font-medium text-amber-200">Daftar Siswa dengan Status
                                Ketidakhadiran</span>
                        </div>
                        <div class="max-h-72 overflow-y-auto">
                            <template x-if="updateLoading">
                                <div class="p-6 text-center text-amber-300 text-sm">
                                    <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-amber-400" fill="none"
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
                            <template x-if="!updateLoading && absentStudents.length === 0">
                                <div class="p-6 text-center text-amber-300 text-sm">
                                    <svg class="w-10 h-10 mx-auto mb-2 text-amber-400" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    Tidak ada data ketidakhadiran untuk kelas ini
                                </div>
                            </template>
                            <table x-show="!updateLoading && absentStudents.length > 0" class="w-full">
                                <thead class="bg-amber-800/40 sticky top-0">
                                    <tr>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-amber-200 uppercase tracking-wider">
                                            Siswa</th>
                                        <th
                                            class="px-4 py-3 text-left text-xs font-semibold text-amber-200 uppercase tracking-wider">
                                            NIS</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-amber-200 uppercase tracking-wider">
                                            Status Saat Ini</th>
                                        <th
                                            class="px-4 py-3 text-center text-xs font-semibold text-amber-200 uppercase tracking-wider">
                                            Status Baru</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-amber-500/20">
                                    <template x-for="(student, index) in absentStudents" :key="student.id">
                                        <tr class="hover:bg-amber-800/20">
                                            <td class="px-4 py-3">
                                                <span class="text-sm text-white font-medium" x-text="student.name"></span>
                                            </td>
                                            <td class="px-4 py-3">
                                                <span class="text-xs text-slate-400 font-mono" x-text="student.nis"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium"
                                                    :class="{
                                                                                                                                                                                                                                                    'bg-purple-500/20 text-purple-300 border border-purple-500/30': student.current_status === 'sakit',
                                                                                                                                                                                                                                                    'bg-cyan-500/20 text-cyan-300 border border-cyan-500/30': student.current_status === 'izin',
                                                                                                                                                                                                                                                    'bg-red-500/20 text-red-300 border border-red-500/30': student.current_status === 'alpha',
                                                                                                                                                                                                                                                    'bg-rose-500/20 text-rose-300 border border-rose-500/30': student.current_status === 'bolos'
                                                                                                                                                                                                                                                }"
                                                    x-text="student.current_status.charAt(0).toUpperCase() + student.current_status.slice(1)"></span>
                                            </td>
                                            <td class="px-4 py-3 text-center">
                                                <input type="hidden" :name="'students['+index+'][attendance_id]'"
                                                    :value="student.attendance_id">
                                                <select :name="'students['+index+'][status]'" x-model="student.new_status"
                                                    class="text-sm px-3 py-1.5 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50">
                                                    <option value="">-- Tidak Ubah --</option>
                                                    <option value="hadir">Hadir</option>
                                                    <option value="sakit">Sakit</option>
                                                    <option value="izin">Izin</option>
                                                    <option value="alpha">Alpha</option>
                                                    <option value="delete">🗑️ Hapus</option>
                                                </select>
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div class="flex justify-between items-center pt-5 border-t border-amber-500/30">
                        <span class="text-sm text-amber-300" x-text="getUpdateCount() + ' siswa akan diupdate'"></span>
                        <div class="flex gap-3">
                            <button type="button" @click="showUpdateModal = false"
                                class="px-5 py-2.5 bg-amber-800/50 border border-amber-500/30 text-white font-medium rounded-xl hover:bg-amber-800 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="getUpdateCount() === 0"
                                class="px-5 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20 disabled:opacity-50 disabled:cursor-not-allowed disabled:shadow-none">
                                Update
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Export Excel Modal -->
        <div x-cloak x-show="showExportModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showExportModal = false">

            <div x-show="showExportModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-2xl bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl shadow-2xl border border-slate-700/50">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Export Data Absensi</h3>
                    <button @click="showExportModal = false" class="text-slate-400 hover:text-white transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.attendance.export') }}" method="GET" class="p-6 space-y-6">
                    <div class="space-y-6">
                        <!-- Tanggal -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal</label>
                            <input type="date" name="date" value="{{ $date }}"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50 date-white-icon">
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Kelas</label>
                            <select name="kelas_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-emerald-500/50">
                                <option value="">Semua Kelas</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="flex gap-4 pt-4 border-t border-slate-700/50 mt-4">
                        <button type="button" @click="showExportModal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/20">
                            Export Excel
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Print Status Modal -->
        <div x-cloak x-show="showPrintStatusModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-start justify-center p-4 pt-20 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showPrintStatusModal = false">

            <div x-show="showPrintStatusModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md bg-gradient-to-br from-slate-900 via-slate-800 to-slate-900 rounded-2xl shadow-2xl border border-slate-700/50">

                <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Cetak Status Kehadiran</h3>
                    <button @click="showPrintStatusModal = false"
                        class="text-slate-400 hover:text-white transition-colors cursor-pointer">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <div class="p-6 space-y-4">
                    <p class="text-sm text-slate-400 mb-4">Pilih status yang ingin dicetak:</p>

                    <a href="{{ route('admin.attendance.printBolos', ['date' => $date]) }}" target="_blank"
                        class="flex items-center gap-3 w-full px-4 py-3 bg-rose-500/10 border border-rose-500/30 rounded-xl text-rose-400 hover:bg-rose-500/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <div>
                            <span class="font-medium">Bolos</span>
                            <p class="text-xs text-slate-500">Siswa yang masuk tapi tidak pulang</p>
                        </div>
                    </a>

                    <a href="{{ route('admin.attendance.printBelumMasuk', ['date' => $date]) }}" target="_blank"
                        class="flex items-center gap-3 w-full px-4 py-3 bg-orange-500/10 border border-orange-500/30 rounded-xl text-orange-400 hover:bg-orange-500/20 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
                        </svg>
                        <div>
                            <span class="font-medium">Belum Masuk</span>
                            <p class="text-xs text-slate-500">Siswa yang belum absen sama sekali</p>
                        </div>
                    </a>

                    <div class="pt-4 border-t border-slate-700/50">
                        <button type="button" @click="showPrintStatusModal = false"
                            class="w-full px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                            Tutup
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <style>
            .date-white-icon::-webkit-calendar-picker-indicator {
                filter: invert(1);
                cursor: pointer;
            }

            /* Scrollable table with fixed header */
            .attendance-table {
                display: flex;
                flex-direction: column;
            }

            .attendance-table thead,
            .attendance-table tbody,
            .attendance-table tfoot {
                display: table;
                width: 100%;
                table-layout: fixed;
            }

            .attendance-table tbody {
                display: block;
                max-height: 100vh;
                overflow-y: auto;
            }

            .attendance-table tbody tr {
                display: table;
                width: 100%;
                table-layout: fixed;
            }

            .attendance-table thead {
                position: sticky;
                top: 0;
                z-index: 10;
            }
        </style>

        <script>
            console.log('Attendance script loaded');
            window.attendancePage = function () {
                console.log('attendancePage initialized');
                return {
                    showAbsenceModal: false,
                    kelasId: '',
                    absenceDate: new Date().toISOString().split('T')[0],
                    isLoading: false,
                    students: [],

                    // Update modal state
                    showUpdateModal: false,
                    updateKelasId: '',
                    updateDate: new Date().toISOString().split('T')[0],
                    updateLoading: false,
                    absentStudents: [],

                    // Export modal
                    showExportModal: false,

                    // Print Status modal
                    showPrintStatusModal: false,

                    init() {
                        this.$watch('showAbsenceModal', value => {
                            document.body.classList.toggle('overflow-hidden', value);
                            if (!value) {
                                this.kelasId = '';
                                this.absenceDate = new Date().toISOString().split('T')[0];
                                this.students = [];
                            }
                        });
                        this.$watch('kelasId', value => this.loadStudents(value));

                        this.$watch('showUpdateModal', value => {
                            document.body.classList.toggle('overflow-hidden', value);
                            if (!value) {
                                this.updateKelasId = '';
                                this.updateDate = '{{ $date }}';
                                this.absentStudents = [];
                            }
                        });
                        this.$watch('updateKelasId', value => this.loadAbsentStudents());

                        this.$watch('showExportModal', value => {
                            document.body.classList.toggle('overflow-hidden', value);
                        });
                    this.$watch('showPrintStatusModal', value => {
                                    document.body.classList.toggle('overflow-hidden', value);
                                });
                            },

                            loadStudents(kelasId) {
                                if (!kelasId) {
                                    this.students = [];
                                    return;
                                }

                                this.isLoading = true;
                                this.students = [];

                                fetch(`{{ route('admin.attendance.students-by-class') }}?kelas_id=${kelasId}&date=${this.absenceDate}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        this.students = data.map(s => ({ ...s, status: '' }));
                                        this.isLoading = false;
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        this.isLoading = false;
                                    });
                            },

                            setAllStatus(status) {
                                this.students = this.students.map(s => ({ ...s, status: status }));
                            },

                            getSelectedCount() {
                                return this.students.filter(s => s.status !== '').length;
                            },

                            // Update modal methods
                            loadAbsentStudents() {
                                if (!this.updateKelasId) {
                                    this.absentStudents = [];
                                    return;
                                }

                                this.updateLoading = true;
                                this.absentStudents = [];

                                fetch(`{{ route('admin.attendance.absent-students') }}?kelas_id=${this.updateKelasId}&date=${this.updateDate}`)
                                    .then(response => response.json())
                                    .then(data => {
                                        this.absentStudents = data.map(s => ({ ...s, new_status: '' }));
                                        this.updateLoading = false;
                                    })
                                    .catch(error => {
                                        console.error('Error:', error);
                                        this.updateLoading = false;
                                    });
                            },

                            getUpdateCount() {
                                return this.absentStudents.filter(s => s.new_status !== '').length;
                            }
                        }
                    }
                </script>

                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
                        class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        {{ session('success') }}
                    </div>
                @endif
            </div>
@endsection