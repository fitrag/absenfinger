@extends('layouts.admin')

@section('title', 'Data Kelas')
@section('page-title', 'Data Kelas')

@section('content')
    <div class="space-y-6" x-data="{ 
                                showModal: false, 
                                editModal: false, 
                                editData: {},
                                search: '',
                                get filteredRows() {
                                    const rows = document.querySelectorAll('tbody tr[data-searchable]');
                                    const term = this.search.toLowerCase();
                                    let visibleCount = 0;
                                    rows.forEach((row, index) => {
                                        const text = row.dataset.searchable.toLowerCase();
                                        const match = text.includes(term);
                                        row.style.display = match ? '' : 'none';
                                        if (match) {
                                            visibleCount++;
                                            row.querySelector('td:first-child').textContent = visibleCount;
                                        }
                                    });
                                    return visibleCount;
                                }
                            }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data Kelas</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data kelas yang terdaftar</p>
            </div>
            <button @click="showModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Kelas
            </button>
        </div>

        <!-- Stats Card -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $kelas->total() }}</p>
                    <p class="text-xs text-slate-400">Total Kelas</p>
                </div>
            </div>
        </div>

        <!-- Search Box and Show Entries -->
        <div class="flex items-center justify-between">
            <!-- Search -->
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-300 whitespace-nowrap">Pencarian</label>
                <div class="relative w-64">
                    <input type="text" x-model="search" @input="filteredRows" placeholder="Cari..."
                        class="w-full px-3 pr-8 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    <template x-if="search.length > 0">
                        <button @click="search = ''; filteredRows"
                            class="absolute inset-y-0 right-0 pr-2 flex items-center">
                            <svg class="w-4 h-4 text-slate-400 hover:text-white" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </template>
                </div>
            </div>

            <!-- Show Entries -->
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-300 whitespace-nowrap">Tampilkan</label>
                <select
                    class="px-3 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
                    onchange="window.location.href='{{ route('admin.kelas.index') }}?perPage=' + this.value">
                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                    <option value="30" {{ request('perPage') == 30 ? 'selected' : '' }}>30</option>
                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ request('perPage') == 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">ID
                            </th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Nama Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Alias</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">
                                Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($kelas as $index => $item)
                            <tr class="hover:bg-slate-800/30 transition-colors"
                                data-searchable="{{ $item->nm_kls }} {{ $item->alias }}">
                                <td class="px-4 py-3 text-sm text-slate-400">
                                    {{ $kelas->firstItem() + $index }}
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">
                                    {{ $item->id }}
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-8 h-8 rounded-lg bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center text-white font-medium text-xs">
                                            {{ strtoupper(substr($item->nm_kls, 0, 1)) }}
                                        </div>
                                        <span class="text-sm font-medium text-white">{{ $item->nm_kls }}</span>
                                    </div>
                                </td>
                                <td class="px-4 py-3">
                                    <span
                                        class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-blue-500/10 text-blue-400 border border-blue-500/20">
                                        {{ $item->alias }}
                                    </span>
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-1">
                                        <a href="{{ route('admin.kelas.show', $item) }}"
                                            class="p-1.5 text-slate-400 hover:text-blue-400 transition-colors" title="Detail">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                            </svg>
                                        </a>
                                        <a href="{{ route('admin.kelas.edit', $item) }}"
                                            class="p-1.5 text-slate-400 hover:text-amber-400 transition-colors" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </a>
                                        <form action="{{ route('admin.kelas.destroy', $item) }}" method="POST"
                                            onsubmit="return confirm('Yakin ingin menghapus kelas ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="p-1.5 text-slate-400 hover:text-rose-400 transition-colors"
                                                title="Hapus">
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
                                            d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data kelas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($kelas->hasPages())
                <div class="px-4 py-3 border-t border-slate-800/50">
                    {{ $kelas->links() }}
                </div>
            @endif
        </div>

        <!-- Add Kelas Modal -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
            @click.self="showModal = false" style="display: none;">

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="w-64">

                <div class="rounded-2xl bg-slate-900 border border-slate-800/50 shadow-2xl overflow-hidden">
                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/50">
                        <h3 class="text-lg font-bold text-white">Tambah Kelas Baru</h3>
                        <button @click="showModal = false" class="p-1 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form action="{{ route('admin.kelas.store') }}" method="POST" class="p-6 space-y-5"
                        x-data="{ nm_kls: '{{ old('nm_kls') }}', alias: '{{ old('alias') }}' }">
                        @csrf

                        <!-- Nama Kelas -->
                        <div>
                            <label for="nm_kls" class="block text-sm font-medium text-slate-300 mb-2">
                                Nama Kelas <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" id="nm_kls" name="nm_kls" x-model="nm_kls"
                                @input="alias = nm_kls.replace(/\s+/g, '').substring(0, 3).toUpperCase()" maxlength="15"
                                required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
                                placeholder="Contoh: X IPA 1">
                            @error('nm_kls')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">Maksimal 15 karakter</p>
                        </div>

                        <!-- Alias -->
                        <div>
                            <label for="alias" class="block text-sm font-medium text-slate-300 mb-2">
                                Alias <span class="text-rose-400">*</span>
                            </label>
                            <input type="text" id="alias" name="alias" x-model="alias" maxlength="6" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
                                placeholder="Contoh: XIA1">
                            @error('alias')
                                <p class="mt-1 text-sm text-rose-400">{{ $message }}</p>
                            @enderror
                            <p class="mt-1 text-xs text-slate-500">Maksimal 6 karakter</p>
                        </div>

                        <!-- Modal Footer -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                                Simpan Kelas
                            </button>
                            <button type="button" @click="showModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <script>
            document.addEventListener('alpine:init', () => {
                // Open modal if there are validation errors
                Alpine.store('openModal', true);
            });
        </script>
        <div x-data x-init="showModal = true"></div>
    @endif
@endsection