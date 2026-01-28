@extends('layouts.admin')

@section('title', 'Daftar Surat Izin Siswa PKL')

@section('page-title', 'Daftar Surat Izin Siswa PKL')

@section('content')
    <div class="space-y-6" x-data="{ showModal: false, selectedIzin: null, action: '' }">
        <!-- Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm">Menunggu Persetujuan</p>
                        <p class="text-2xl font-bold text-amber-400">{{ $totalPending }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-amber-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm">Disetujui</p>
                        <p class="text-2xl font-bold text-emerald-400">{{ $totalDisetujui }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
            <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-slate-400 text-sm">Ditolak</p>
                        <p class="text-2xl font-bold text-rose-400">{{ $totalDitolak }}</p>
                    </div>
                    <div class="w-12 h-12 rounded-xl bg-rose-500/10 flex items-center justify-center">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filters -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4 sm:p-6">
            <form action="{{ route('admin.guru.pkl.surat_izin') }}" method="GET"
                class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Tahun Pelajaran</label>
                    <select name="tp_id"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 text-sm"
                        onchange="this.form.submit()">
                        @foreach ($tahunPelajarans as $tp)
                            <option value="{{ $tp->id }}" {{ $selectedTp?->id == $tp->id ? 'selected' : '' }}>
                                {{ $tp->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">DUDI</label>
                    <select name="dudi_id"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 text-sm"
                        onchange="this.form.submit()">
                        <option value="">Semua DUDI</option>
                        @foreach ($dudiList as $dudi)
                            <option value="{{ $dudi->id }}" {{ request('dudi_id') == $dudi->id ? 'selected' : '' }}>
                                {{ $dudi->nama }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Status</label>
                    <select name="status"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 text-sm"
                        onchange="this.form.submit()">
                        <option value="">Semua Status</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                        <option value="disetujui" {{ request('status') == 'disetujui' ? 'selected' : '' }}>Disetujui
                        </option>
                        <option value="ditolak" {{ request('status') == 'ditolak' ? 'selected' : '' }}>Ditolak</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Cari</label>
                    <div class="relative">
                        <input type="text" name="search" value="{{ request('search') }}"
                            placeholder="Cari nama/NIS..."
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 text-sm pr-10">
                        <button type="submit" class="absolute right-3 top-1/2 -translate-y-1/2 text-slate-400">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                            </svg>
                        </button>
                    </div>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-700/50">
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Siswa</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">DUDI</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Jenis</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Periode</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-medium text-slate-400 uppercase">File</th>
                            <th class="px-4 py-3 text-center text-xs font-medium text-slate-400 uppercase">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse ($suratIzins as $izin)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-500 flex items-center justify-center text-white font-bold text-sm">
                                            {{ substr($izin->student->name ?? 'N', 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="text-white font-medium text-sm">{{ $izin->student->name ?? '-' }}
                                            </p>
                                            <p class="text-slate-400 text-xs">{{ $izin->student->nis ?? '-' }} •
                                                {{ $izin->student->kelas->nama ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-slate-300 text-sm">{{ $izin->pkl->dudi->nama ?? '-' }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if ($izin->jenis_izin == 'sakit') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                        @elseif($izin->jenis_izin == 'izin') bg-blue-500/10 text-blue-400 border border-blue-500/20
                                        @else bg-slate-500/10 text-slate-400 border border-slate-500/20 @endif">
                                        {{ $izin->jenis_izin_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="text-slate-300 text-sm">{{ $izin->tanggal_mulai->format('d M Y') }}</p>
                                    @if ($izin->tanggal_mulai != $izin->tanggal_selesai)
                                        <p class="text-slate-500 text-xs">s/d {{ $izin->tanggal_selesai->format('d M Y') }}
                                        </p>
                                    @endif
                                    <p class="text-slate-400 text-xs">({{ $izin->jumlah_hari }} hari)</p>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium
                                        @if ($izin->status == 'pending') bg-amber-500/10 text-amber-400 border border-amber-500/20
                                        @elseif($izin->status == 'disetujui') bg-emerald-500/10 text-emerald-400 border border-emerald-500/20
                                        @else bg-rose-500/10 text-rose-400 border border-rose-500/20 @endif">
                                        {{ $izin->status_label }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    @if ($izin->file_path)
                                        <a href="{{ route('admin.guru.pkl.surat_izin.preview', $izin->id) }}"
                                            target="_blank"
                                            class="inline-flex items-center gap-1 text-blue-400 text-sm hover:underline">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                            Lihat
                                        </a>
                                    @else
                                        <span class="text-slate-500 text-sm">-</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-center">
                                    @if ($izin->status == 'pending')
                                        <div class="flex items-center justify-center gap-2">
                                            <button type="button"
                                                @click="showModal = true; selectedIzin = {{ $izin->id }}; action = 'disetujui'"
                                                class="p-2 bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 rounded-lg border border-emerald-500/30 transition-colors"
                                                title="Setujui">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M5 13l4 4L19 7" />
                                                </svg>
                                            </button>
                                            <button type="button"
                                                @click="showModal = true; selectedIzin = {{ $izin->id }}; action = 'ditolak'"
                                                class="p-2 bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 rounded-lg border border-rose-500/30 transition-colors"
                                                title="Tolak">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M6 18L18 6M6 6l12 12" />
                                                </svg>
                                            </button>
                                        </div>
                                    @else
                                        <span class="text-slate-500 text-xs">-</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-8 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                    </svg>
                                    <p class="text-slate-400 text-sm">Belum ada surat izin dari siswa bimbingan.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if ($suratIzins->hasPages())
                <div class="px-4 py-3 border-t border-slate-700/50">
                    {{ $suratIzins->links() }}
                </div>
            @endif
        </div>

        <!-- Approval Modal -->
        <div x-show="showModal" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm"
            x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-150"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0">
            <div @click.away="showModal = false"
                class="bg-slate-900 border border-slate-700 rounded-2xl w-full max-w-md mx-4 p-6">
                <h3 class="text-lg font-bold text-white mb-4"
                    x-text="action === 'disetujui' ? 'Setujui Surat Izin' : 'Tolak Surat Izin'"></h3>

                <form :action="'/admin/guru/pkl/surat-izin/' + selectedIzin + '/update-status'" method="POST">
                    @csrf
                    <input type="hidden" name="status" :value="action">

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-300 mb-2">Catatan (Opsional)</label>
                        <textarea name="catatan_guru" rows="3" placeholder="Tambahkan catatan jika diperlukan..."
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-slate-300 focus:ring-2 focus:ring-blue-500/50 text-sm resize-none"></textarea>
                    </div>

                    <div class="flex justify-end gap-3">
                        <button type="button" @click="showModal = false"
                            class="px-4 py-2 text-slate-400 hover:text-white hover:bg-slate-700 rounded-xl transition-colors text-sm">
                            Batal
                        </button>
                        <button type="submit"
                            :class="action === 'disetujui' ?
                                'bg-emerald-500 hover:bg-emerald-600' :
                                'bg-rose-500 hover:bg-rose-600'"
                            class="px-4 py-2 text-white rounded-xl transition-colors text-sm font-medium">
                            <span x-text="action === 'disetujui' ? 'Setujui' : 'Tolak'"></span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Flash Messages -->
    @if (session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if (session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection
