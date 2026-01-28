@extends('layouts.frontend')

@section('title', 'Detail Pelanggaran - ' . $student->name)

@section('content')
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Back Button & Header -->
        <div class="mb-6">
            <a href="{{ route('frontend.kesiswaan') }}"
                class="inline-flex items-center gap-2 text-slate-400 hover:text-white transition-colors mb-4">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                </svg>
                Kembali ke Kesiswaan
            </a>
            <div class="flex items-center gap-4">
                <div
                    class="w-14 h-14 rounded-xl bg-gradient-to-br from-rose-500/30 to-rose-600/20 flex items-center justify-center">
                    <svg class="w-7 h-7 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $student->name }}</h1>
                    <p class="text-slate-400">{{ $student->nis }} • {{ $student->kelas->nm_kls ?? '-' }}</p>
                </div>
            </div>
        </div>

        <!-- Summary Card -->
        <div class="grid grid-cols-2 gap-4 mb-6">
            <div class="rounded-xl bg-gradient-to-br from-rose-500/20 to-rose-600/10 border border-rose-500/30 p-5">
                <p class="text-3xl font-bold text-white">{{ $totalPoin }}</p>
                <p class="text-sm text-slate-400">Total Poin Pelanggaran</p>
            </div>
            <div class="rounded-xl bg-gradient-to-br from-amber-500/20 to-amber-600/10 border border-amber-500/30 p-5">
                <p class="text-3xl font-bold text-white">{{ $pelanggarans->count() }}</p>
                <p class="text-sm text-slate-400">Jumlah Pelanggaran</p>
            </div>
        </div>

        <!-- Pelanggaran List -->
        <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-800/50">
                <h2 class="text-lg font-semibold text-white">Riwayat Pelanggaran</h2>
            </div>
            <div class="divide-y divide-slate-800/50">
                @forelse($pelanggarans as $pelanggaran)
                    <div class="p-4 hover:bg-slate-800/30 transition-colors">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="font-medium text-rose-400">{{ $pelanggaran->jenis_pelanggaran }}</span>
                                    <span
                                        class="inline-flex px-2 py-0.5 text-xs rounded-full {{ $pelanggaran->status_badge }} border">
                                        {{ $pelanggaran->status_label }}
                                    </span>
                                </div>
                                @if($pelanggaran->deskripsi)
                                    <p class="text-sm text-slate-400 mt-1">{{ $pelanggaran->deskripsi }}</p>
                                @endif
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span
                                    class="inline-flex px-2 py-1 text-xs font-bold rounded-lg {{ $pelanggaran->poin_badge }} border">
                                    {{ $pelanggaran->poin }} Poin
                                </span>
                                <p class="text-xs text-slate-500 mt-1">{{ $pelanggaran->tanggal->format('d M Y') }}</p>
                            </div>
                        </div>

                        {{-- Foto Bukti dan TTD Siswa --}}
                        <div class="mt-3 flex gap-3">
                            <div class="w-32 flex-shrink-0">
                                <p class="text-xs text-slate-500 mb-1">Foto Bukti:</p>
                                @if($pelanggaran->foto_bukti)
                                    <button type="button" onclick="openImageModal('{{ asset('storage/' . $pelanggaran->foto_bukti) }}', 'Foto Bukti')"
                                        class="block w-full rounded-lg overflow-hidden border border-slate-700/50 hover:border-rose-500/50 transition-colors cursor-pointer">
                                        <img src="{{ asset('storage/' . $pelanggaran->foto_bukti) }}" alt="Foto Bukti"
                                            class="w-full h-40 object-cover">
                                    </button>
                                @else
                                    <div class="rounded-lg overflow-hidden border border-slate-700/50 bg-slate-800/50 h-40 flex items-center justify-center">
                                        <div class="text-center text-slate-500">
                                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <div class="w-32 flex-shrink-0">
                                <p class="text-xs text-slate-500 mb-1">TTD Siswa:</p>
                                @if($pelanggaran->ttd_siswa)
                                    <button type="button" onclick="openImageModal('{{ $pelanggaran->ttd_siswa }}', 'Tanda Tangan Siswa')"
                                        class="block w-full rounded-lg overflow-hidden border border-slate-700/50 hover:border-rose-500/50 bg-white p-1 cursor-pointer transition-colors">
                                        <img src="{{ $pelanggaran->ttd_siswa }}" alt="TTD Siswa" class="w-full h-32 object-contain">
                                    </button>
                                @else
                                    <div class="rounded-lg overflow-hidden border border-slate-700/50 bg-slate-800/50 h-40 flex items-center justify-center">
                                        <div class="text-center text-slate-500">
                                            <svg class="w-6 h-6 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                                    d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-8 text-center">
                        <p class="text-slate-500">Tidak ada data pelanggaran</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 z-50 hidden items-start justify-center pt-16 pb-8 px-4 bg-black/80 backdrop-blur-sm overflow-y-auto" onclick="closeImageModal(event)">
        <div class="relative max-w-3xl w-full">
            <!-- Close Button -->
            <button type="button" onclick="closeImageModal()" 
                class="fixed top-4 right-4 p-2 text-white/70 hover:text-white transition-colors z-50 bg-black/50 rounded-full">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
            <!-- Modal Title -->
            <p id="modalTitle" class="text-white/70 text-sm font-medium mb-3"></p>
            <!-- Image Container -->
            <div class="bg-white rounded-xl overflow-hidden shadow-2xl">
                <img id="modalImage" src="" alt="" class="w-full h-auto object-contain">
            </div>
        </div>
    </div>

    <script>
        function openImageModal(src, title) {
            const modal = document.getElementById('imageModal');
            const modalImage = document.getElementById('modalImage');
            const modalTitle = document.getElementById('modalTitle');
            
            modalImage.src = src;
            modalTitle.textContent = title;
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }

        function closeImageModal(event) {
            if (event && event.target !== event.currentTarget) return;
            
            const modal = document.getElementById('imageModal');
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = '';
        }

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
@endsection
