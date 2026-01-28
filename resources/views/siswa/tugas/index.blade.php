@extends('layouts.admin')

@section('title', 'Tugas Saya')

@section('content')
    <div x-data="{
                                    openModal: false,
                                    selectedTugasId: null,
                                    selectedTugasJudul: '',
                                    submitTugas(id, judul) {
                                        this.selectedTugasId = id;
                                        this.selectedTugasJudul = judul;
                                        this.openModal = true;
                                    }
                                }">
        <!-- Header -->
        <div class="mb-6 flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Daftar Tugas</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola dan kumpulkan tugas sekolah Anda</p>
            </div>
        </div>

        <!-- Filters/Status -->
        <div class="mb-6 flex gap-2">
            <a href="{{ route('siswa.tugas.index', ['status' => 'all']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status', 'all') == 'all' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Semua
            </a>
            <a href="{{ route('siswa.tugas.index', ['status' => 'pending']) }}"
                class="px-4 py-2 rounded-lg text-sm font-medium transition-colors {{ request('status') == 'pending' ? 'bg-blue-600 text-white' : 'bg-slate-800 text-slate-400 hover:text-white' }}">
                Belum Dikumpulkan
            </a>
            {{-- Add logic in controller to actually filter if needed, currently view handles display --}}
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-green-500/10 border border-green-500/20 text-green-400 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-500/10 border border-red-500/20 text-red-400 rounded-xl flex items-center gap-3">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
                {{ session('error') }}
            </div>
        @endif

        <!-- Assignments Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($tugasList as $tugas)
                @php
                    $submission = $tugas->submissions->first();
                    $isSubmitted = $submission != null;
                    $isLate = $isSubmitted && $submission->submitted_at > $tugas->created_at; // Logic check for late? submission->submitted_at vs deadline
                    // Deadline parsing - extract date only from tanggal_deadline before combining with jam_deadline
                    $deadline = \Carbon\Carbon::parse(\Carbon\Carbon::parse($tugas->tanggal_deadline)->format('Y-m-d') . ' ' . $tugas->jam_deadline);
                    $isDeadlinePassed = now() > $deadline;
                    $statusColor = $isSubmitted ? 'text-green-400 bg-green-500/10 border-green-500/20' : ($isDeadlinePassed ? 'text-red-400 bg-red-500/10 border-red-500/20' : 'text-amber-400 bg-amber-500/10 border-amber-500/20');
                    $statusText = $isSubmitted ? 'Sudah Dikumpulkan' : ($isDeadlinePassed ? 'Terlambat (Belum)' : 'Belum Dikumpulkan');
                @endphp
                <div
                    class="bg-slate-900 rounded-xl border border-slate-800 p-5 hover:border-blue-500/30 transition-all duration-300 group">
                    <div class="flex items-start justify-between mb-4">
                        <div>
                            <span
                                class="inline-block px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-800 text-slate-300 mb-2">
                                {{ $tugas->mapel->nama_mapel }}
                            </span>
                            <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors line-clamp-2">
                                {{ $tugas->judul }}
                            </h3>
                        </div>
                        <!-- Status Badge -->
                        <div class="px-2.5 py-1 rounded-lg text-xs font-medium border {{ $statusColor }}">
                            {{ $statusText }}
                        </div>
                    </div>

                    <div class="space-y-3 mb-6">
                        <div class="flex items-center gap-2 text-sm text-slate-400">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ $tugas->guru->nama_guru }}</span>
                        </div>
                        <div class="flex items-center gap-2 text-sm text-slate-400">
                            <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <span>Deadline: {{ $deadline->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        @if($tugas->file_path)
                            <a href="{{ asset('storage/' . $tugas->file_path) }}" target="_blank"
                                class="flex items-center gap-2 text-sm text-blue-400 hover:underline">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                Download Soal
                            </a>
                        @endif
                    </div>

                    <div class="border-t border-slate-800 pt-4 flex items-center justify-between">
                        @if($isSubmitted)
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-500">Dikumpulkan pada:</span>
                                <span
                                    class="text-sm text-green-400">{{ $submission->submitted_at->translatedFormat('d M Y, H:i') }}</span>
                                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank"
                                    class="text-xs text-blue-400 hover:underline mt-1">Lihat File Saya</a>
                                {{-- Tampilan Nilai --}}
                                <div class="mt-2 pt-2 border-t border-slate-800">
                                    <span class="text-xs text-slate-500">Nilai:</span>
                                    @if($submission->nilai !== null)
                                        <span class="ml-1 px-2 py-0.5 rounded bg-emerald-500/20 text-emerald-400 font-bold text-sm">
                                            {{ $submission->nilai }}
                                        </span>
                                    @else
                                        <span class="ml-1 text-xs text-slate-500 italic">Belum dinilai</span>
                                    @endif
                                </div>
                            </div>
                            {{-- Allow resubmission if not deadline passed or just always? Let's allow if not passed --}}
                            @if(!$isDeadlinePassed)
                                <button @click="submitTugas({{ $tugas->id }}, '{{ addslashes($tugas->judul) }}')"
                                    class="text-sm text-slate-400 hover:text-white transition-colors cursor-pointer">
                                    Ubah
                                </button>
                            @endif
                        @else
                            @if(!$isDeadlinePassed)
                                <button @click="submitTugas({{ $tugas->id }}, '{{ addslashes($tugas->judul) }}')"
                                    class="w-full bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg text-sm font-medium transition-all duration-300 shadow-lg shadow-blue-600/20 cursor-pointer">
                                    Kumpulkan Tugas
                                </button>
                            @else
                                <span class="text-sm text-red-400 italic">Deadline Terlewat</span>
                            @endif
                        @endif
                    </div>
                </div>
            @empty
                <div class="col-span-full py-12 text-center">
                    <div class="w-24 h-24 bg-slate-800/50 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-12 h-12 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                    </div>
                    <h3 class="text-lg font-medium text-white mb-1">Belum ada tugas</h3>
                    <p class="text-slate-500">Saat ini belum ada tugas untuk kelas Anda.</p>
                </div>
            @endforelse
        </div>

        <!-- Pagination -->
        <div class="mt-8">
            {{ $tugasList->links() }}
        </div>

        <!-- Submit Modal -->
        <div x-show="openModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">

            <!-- Backdrop -->
            <div class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="openModal = false"></div>

            <!-- Modal Container -->
            <div class="relative flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <!-- Modal Panel -->
                <div class="relative z-10 bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl shadow-2xl"
                    @click.stop x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">

                    <div class="flex items-center justify-between mb-6">
                        <h2 class="text-xl font-bold text-white">Kumpulkan Tugas</h2>
                        <button @click="openModal = false"
                            class="text-slate-400 hover:text-white transition-colors cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form :action="`/siswa/tugas/${selectedTugasId}/submit`" method="POST" enctype="multipart/form-data"
                        class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">
                                Judul Tugas
                            </label>
                            <input type="text" :value="selectedTugasJudul" disabled
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-2.5 text-white sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 cursor-not-allowed opacity-75">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">
                                Keterangan (Opsional)
                            </label>
                            <textarea name="keterangan" rows="3"
                                class="w-full bg-slate-800/50 border border-slate-700 rounded-xl px-4 py-2.5 text-white sm:text-sm focus:border-blue-500 focus:ring-1 focus:ring-blue-500 placeholder-slate-500"
                                placeholder="Tambahkan catatan untuk guru..."></textarea>
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-slate-400 mb-2">
                                File Tugas <span class="text-red-400">*</span>
                            </label>
                            <div class="relative group">
                                <div
                                    class="absolute inset-0 bg-gradient-to-r from-blue-500/20 to-purple-500/20 rounded-xl blur-lg transition-opacity opacity-0 group-hover:opacity-100">
                                </div>
                                <input type="file" name="file" required
                                    class="relative w-full bg-slate-800 border border-slate-700 rounded-xl px-4 py-3 text-sm text-slate-300 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-xs file:font-semibold file:bg-blue-600 file:text-white hover:file:bg-blue-500 cursor-pointer transition-all">
                                <p class="mt-2 text-xs text-slate-500">Format: PDF, DOC, DOCX, JPG, PNG (Max 10MB)</p>
                            </div>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="openModal = false"
                                class="px-4 py-2 rounded-xl text-sm font-medium text-slate-400 hover:text-white hover:bg-slate-800 transition-colors">
                                Batal
                            </button>
                            <button type="submit"
                                class="px-6 py-2 rounded-xl text-sm font-medium text-white bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-500 hover:to-purple-500 shadow-lg shadow-blue-500/20 transition-all duration-300">
                                Kirim Tugas
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection