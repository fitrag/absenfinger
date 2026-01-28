@extends('layouts.admin')

@section('title', 'Detail Presensi Kelas ' . $kelas->nm_kls)
@section('page-title', 'Detail Presensi Kelas')

@section('content')
    <div class="space-y-6" x-data="{
        showEditModal: false,
        editStudent: { nis: '', name: '', status: '', date: '{{ $date }}', checkIn: '', checkOut: '', checktype: 'masuk' },
        selectedStudents: [],
        bulkStatus: '',
        bulkChecktype: 'pulang',
        selectAll: false,
        openEditModal(nis, name, status, checkIn = '', checkOut = '') {
            this.editStudent.nis = nis;
            this.editStudent.name = name;
            this.editStudent.status = status;
            this.editStudent.checkIn = checkIn;
            this.editStudent.checkOut = checkOut;
            this.editStudent.checktype = 'masuk';
            this.showEditModal = true;
        },
        toggleSelectAll() {
            if (this.selectAll) {
                this.selectedStudents = [...document.querySelectorAll('input[name=student_checkbox]')].map(el => el.value);
            } else {
                this.selectedStudents = [];
            }
        },
        updateSelectAll() {
            const total = document.querySelectorAll('input[name=student_checkbox]').length;
            this.selectAll = this.selectedStudents.length === total;
        }
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Detail Presensi Kelas {{ $kelas->nm_kls }}</h2>
                <p class="text-sm text-slate-400 mt-1">
                    Tanggal {{ \Carbon\Carbon::parse($date)->translatedFormat('l, d F Y') }}
                </p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.attendance.index', ['date' => $date]) }}"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                    </svg>
                    Kembali
                </a>
            </div>
        </div>

        <!-- Filter -->
        <div class="bg-slate-900/50 border border-slate-800/50 rounded-xl p-4">
            <form action="{{ route('admin.attendance.showByKelas', $kelas->id) }}" method="GET"
                class="flex flex-wrap items-center gap-3">
                <!-- Date -->
                <input type="date" name="date" value="{{ $date }}" onchange="this.form.submit()"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50 date-white-icon">

                <!-- Kelas -->
                <select name="kelasId"
                    onchange="window.location.href='{{ route('admin.attendance.showByKelas', ['kelasId' => '__KELAS_ID__', 'date' => $date]) }}'.replace('__KELAS_ID__', this.value)"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    @foreach($kelasList as $k)
                        <option value="{{ $k->id }}" {{ $kelas->id == $k->id ? 'selected' : '' }}>
                            {{ $k->nm_kls }}
                        </option>
                    @endforeach
                </select>
            </form>
        </div>

        <!-- Stats -->
        @php
            $hadir = 0;
            $sakit = 0;
            $izin = 0;
            $alpha = 0;
            $bolos = 0;
            $terlambat = 0;
            $tidakHadir = 0;
            $tidakAbsen = 0;
            foreach ($students as $student) {
                $attendance = $attendanceData[$student->nis] ?? null;
                $checkIn = $attendance['check_in'] ?? null;
                $checkOut = $attendance['check_out'] ?? null;

                if (!$checkIn) {
                    $tidakAbsen++;
                } elseif ($checkIn->checktype == 2) {
                    $sakit++;
                } elseif ($checkIn->checktype == 3) {
                    $izin++;
                } elseif ($checkIn->checktype == 4) {
                    $alpha++;
                } else {
                    // Check if PKL attendance (flexible hours)
                    $isPklAttendance = $checkIn->is_pkl ?? false;

                    if ($isPklAttendance) {
                        // PKL: hadir if both check-in and check-out, otherwise bolos
                        if ($checkOut) {
                            $hadir++;
                        } else {
                            $bolos++;
                        }
                    } else {
                        // Regular attendance
                        if ($checkOut) {
                            // Jika ada jam masuk dan jam pulang = Hadir
                            $hadir++;
                        } else {
                            // Hanya checkin tanpa checkout = Bolos
                            $bolos++;
                        }
                    }
                }
            }
        @endphp
        <div class="flex flex-wrap items-center gap-3 text-sm">
            <span class="px-3 py-1.5 rounded-xl bg-slate-800/50 border border-slate-700/50 text-slate-300">
                Total: <span class="font-semibold text-white">{{ $students->count() }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400">
                Hadir: <span class="font-semibold">{{ $hadir }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-purple-500/10 border border-purple-500/30 text-purple-400">
                Sakit: <span class="font-semibold">{{ $sakit }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400">
                Izin: <span class="font-semibold">{{ $izin }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-red-500/10 border border-red-500/30 text-red-400">
                Alpha: <span class="font-semibold">{{ $alpha }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400">
                Bolos: <span class="font-semibold">{{ $bolos }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-amber-500/10 border border-amber-500/30 text-amber-400">
                Terlambat: <span class="font-semibold">{{ $terlambat }}</span>
            </span>
            <span class="px-3 py-1.5 rounded-xl bg-slate-500/10 border border-slate-500/30 text-slate-400">
                Tidak Absen: <span class="font-semibold">{{ $tidakAbsen }}</span>
            </span>
        </div>

        <!-- Bulk Action Bar -->
        <div x-show="selectedStudents.length > 0" x-cloak
            class="flex flex-wrap items-center gap-3 p-4 bg-blue-500/10 border border-blue-500/30 rounded-xl">
            <span class="text-sm text-blue-300">
                <span x-text="selectedStudents.length"></span> siswa dipilih
            </span>
            <form method="POST" action="{{ route('admin.attendance.bulkUpdateAttendance') }}" class="flex flex-wrap items-center gap-2">
                @csrf
                <input type="hidden" name="date" value="{{ $date }}">
                <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">
                <template x-for="nis in selectedStudents" :key="nis">
                    <input type="hidden" name="students[]" :value="nis">
                </template>
                <select name="status" x-model="bulkStatus" required
                    class="px-3 py-1.5 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm focus:outline-none focus:border-blue-500/50">
                    <option value="">-- Pilih Status --</option>
                    <option value="hadir">Hadir</option>
                    <option value="sakit">Sakit</option>
                    <option value="izin">Izin</option>
                    <option value="alpha">Alpha</option>
                </select>
                <!-- Checktype selection when hadir - only Pulang -->
                <div x-show="bulkStatus === 'hadir'" x-cloak class="flex items-center">
                    <input type="hidden" name="checktype" value="pulang">
                    <span class="px-3 py-1.5 rounded-lg text-sm font-medium bg-cyan-500/20 text-cyan-300 border border-cyan-500">Pulang</span>
                </div>
                <button type="submit" :disabled="!bulkStatus"
                    class="px-4 py-1.5 bg-blue-500 text-white font-medium rounded-lg hover:bg-blue-600 transition-all disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer text-sm">
                    Simpan
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider w-10">
                                <input type="checkbox" x-model="selectAll" @change="toggleSelectAll()" {{ isset($disableEdit) && $disableEdit ? 'disabled' : '' }}
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-900 cursor-pointer {{ isset($disableEdit) && $disableEdit ? 'cursor-not-allowed opacity-50' : '' }}">
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                NIS</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Jam Masuk</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Jam Pulang</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($students as $index => $student)
                            @php
                                $attendance = $attendanceData[$student->nis] ?? null;
                                $checkIn = $attendance['check_in'] ?? null;
                                $checkOut = $attendance['check_out'] ?? null;
                                $isPklAttendance = $checkIn && ($checkIn->is_pkl ?? false);
                                $isVeryLate = $checkIn && $checkIn->checktype == 0 && !$isPklAttendance && $checkIn->checktime->format('H:i') > '08:00';
                                $isLate = $checkIn && $checkIn->checktype == 0 && !$isPklAttendance && $checkIn->checktime->format('H:i') > '07:00' && $checkIn->checktime->format('H:i') <= '08:00';
                                $isSakit = $checkIn && $checkIn->checktype == 2;
                                $isIzin = $checkIn && $checkIn->checktype == 3;
                                $isAlpha = $checkIn && $checkIn->checktype == 4;

                                // Determine status for PKL
                                $pklHadir = $isPklAttendance && $checkOut;
                                $pklAlpha = $isPklAttendance && !$checkOut;
                            @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $hasCheckIn = $checkIn && $checkIn->checktype == 0;
                                        $hasCheckOut = $checkOut != null;
                                        $isComplete = $hasCheckIn && $hasCheckOut;
                                    @endphp
                                    @if($isComplete || (isset($disableEdit) && $disableEdit))
                                        <input type="checkbox" disabled
                                            class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-slate-500 cursor-not-allowed opacity-50">
                                    @else
                                        <input type="checkbox" name="student_checkbox" value="{{ $student->nis }}"
                                            x-model="selectedStudents" @change="updateSelectAll()"
                                            class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-blue-500 focus:ring-blue-500 focus:ring-offset-slate-900 cursor-pointer">
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $index + 1 }}</td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-gradient-to-br {{ $checkIn && !($isSakit || $isIzin || $isAlpha || $pklAlpha) ? ($isLate ? 'from-amber-600 to-amber-700' : 'from-emerald-600 to-emerald-700') : 'from-slate-600 to-slate-700' }} flex items-center justify-center text-white font-medium text-xs">
                                            {{ strtoupper(substr($student->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="text-sm font-medium text-white">{{ $student->name }}</span>
                                            @if($student->jen_kel)
                                                <p class="text-xs text-slate-500">
                                                    {{ $student->jen_kel == 'L' ? 'Laki-laki' : 'Perempuan' }}
                                                    @if($isPklAttendance)
                                                        <span class="text-cyan-400">(PKL)</span>
                                                    @endif
                                                </p>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $student->nis }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($checkIn && $checkIn->checktype == 0)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium font-mono {{ $isPklAttendance ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : ($isLate ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20') }}">
                                            {{ $checkIn->checktime->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($checkOut)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium font-mono bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                            {{ $checkOut->checktime->format('H:i:s') }}
                                        </span>
                                    @else
                                        <span class="text-slate-500">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if($isSakit)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">Sakit</span>
                                    @elseif($isIzin)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">Izin</span>
                                    @elseif($isAlpha)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-red-500/10 text-red-400 border border-red-500/20">Alpha</span>
                                    @elseif($checkIn)
                                        @if($isPklAttendance)
                                            {{-- PKL Attendance --}}
                                            @if($checkOut)
                                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Hadir PKL</span>
                                            @else
                                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">Bolos PKL</span>
                                            @endif
                                        @else
                                            {{-- Regular Attendance --}}
                                            @if($checkOut)
                                                {{-- Jika ada jam masuk dan jam pulang = Hadir --}}
                                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Hadir</span>
                                            @else
                                                {{-- Hanya checkin tanpa checkout = Bolos --}}
                                                <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-rose-500/10 text-rose-400 border border-rose-500/20">Bolos</span>
                                            @endif
                                        @endif
                                    @else
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">Tidak
                                            Hadir</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @php
                                        $currentStatus = '';
                                        if ($isSakit)
                                            $currentStatus = 'sakit';
                                        elseif ($isIzin)
                                            $currentStatus = 'izin';
                                        elseif ($isAlpha)
                                            $currentStatus = 'alpha';
                                        elseif ($checkIn && $checkOut)
                                            $currentStatus = 'hadir';
                                        elseif ($checkIn && !$checkOut)
                                            $currentStatus = 'bolos';
                                    @endphp
                                    @php
                                        $hasCheckIn = $checkIn && $checkIn->checktype == 0;
                                    @endphp
                                    @if($hasCheckIn || (isset($disableEdit) && $disableEdit))
                                        <button type="button" disabled
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-slate-500/10 text-slate-500 rounded-lg text-xs font-medium border border-slate-500/20 cursor-not-allowed opacity-50">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                    @else
                                        <button type="button"
                                            @click="openEditModal('{{ $student->nis }}', '{{ addslashes($student->name) }}', '{{ $currentStatus }}', '{{ $checkIn && $checkIn->checktype == 0 ? $checkIn->checktime->format('H:i:s') : '' }}', '{{ $checkOut ? $checkOut->checktime->format('H:i:s') : '' }}')"
                                            class="inline-flex items-center gap-1 px-2 py-1 bg-blue-500/10 text-blue-400 hover:bg-blue-500/20 rounded-lg text-xs font-medium transition-colors border border-blue-500/20 cursor-pointer">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                            Edit
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data siswa di kelas ini</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-cloak x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/60 backdrop-blur-sm">
            <div @click.away="showEditModal = false" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="w-full max-w-md bg-slate-900 border border-slate-700/50 rounded-2xl shadow-2xl overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-semibold text-white">Edit Status Kehadiran</h3>
                    <p class="text-sm text-slate-400 mt-1" x-text="editStudent.name"></p>
                </div>

                <!-- Form -->
                <form method="POST" action="{{ route('admin.attendance.updateSingleAttendance') }}">
                    @csrf
                    <input type="hidden" name="nis" x-model="editStudent.nis">
                    <input type="hidden" name="date" x-model="editStudent.date">
                    <input type="hidden" name="kelas_id" value="{{ $kelas->id }}">

                    <div class="px-6 py-4 space-y-4">
                        <!-- Show attendance times if status is hadir/terlambat/bolos -->
                        <div x-show="editStudent.checkIn || editStudent.checkOut" class="p-4 rounded-xl bg-slate-800/50 border border-slate-700/50">
                            <p class="text-xs text-slate-400 mb-2">Waktu Absen Saat Ini:</p>
                            <div class="flex gap-4">
                                <div x-show="editStudent.checkIn">
                                    <span class="text-xs text-slate-500">Masuk:</span>
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium font-mono bg-teal-500/10 text-teal-400 border border-teal-500/20 ml-1" x-text="editStudent.checkIn"></span>
                                </div>
                                <div x-show="editStudent.checkOut">
                                    <span class="text-xs text-slate-500">Pulang:</span>
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium font-mono bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 ml-1" x-text="editStudent.checkOut"></span>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Status</label>
                            <select name="status" x-model="editStudent.status"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                <option value="">-- Pilih Status --</option>
                                <option value="hadir">Hadir</option>
                                <option value="sakit">Sakit</option>
                                <option value="izin">Izin</option>
                                <option value="alpha">Alpha</option>
                            </select>
                        </div>
                        <!-- Checktype selection when status is hadir -->
                        <div x-show="editStudent.status === 'hadir'" x-cloak>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Update Absen</label>
                            <div class="flex gap-3">
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="checktype" value="masuk" x-model="editStudent.checktype" class="hidden">
                                    <div class="p-3 rounded-xl border text-center transition-all"
                                        :class="editStudent.checktype === 'masuk' ? 'border-teal-500 bg-teal-500/20' : 'border-slate-700/50 bg-slate-800/50'">
                                        <span class="text-sm font-medium" :class="editStudent.checktype === 'masuk' ? 'text-teal-300' : 'text-teal-400'">Masuk</span>
                                        <p class="text-xs mt-1" :class="editStudent.checktype === 'masuk' ? 'text-teal-400' : 'text-slate-400'">{{ \Carbon\Carbon::parse($date)->isToday() ? now()->format('H:i') : '07:00' }}</p>
                                    </div>
                                </label>
                                <label class="flex-1 cursor-pointer">
                                    <input type="radio" name="checktype" value="pulang" x-model="editStudent.checktype" class="hidden">
                                    <div class="p-3 rounded-xl border text-center transition-all"
                                        :class="editStudent.checktype === 'pulang' ? 'border-cyan-500 bg-cyan-500/20' : 'border-slate-700/50 bg-slate-800/50'">
                                        <span class="text-sm font-medium" :class="editStudent.checktype === 'pulang' ? 'text-cyan-300' : 'text-cyan-400'">Pulang</span>
                                        <p class="text-xs mt-1" :class="editStudent.checktype === 'pulang' ? 'text-cyan-400' : 'text-slate-400'">{{ \Carbon\Carbon::parse($date)->isToday() ? now()->format('H:i') : '16:00' }}</p>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 border-t border-slate-700/50 flex justify-end gap-3">
                        <button type="button" @click="showEditModal = false"
                            class="px-4 py-2 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="px-4 py-2 bg-blue-500 text-white font-medium rounded-xl hover:bg-blue-600 transition-all cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <style>
        .date-white-icon::-webkit-calendar-picker-indicator {
            filter: invert(1);
            cursor: pointer;
        }
    </style>
@endsection