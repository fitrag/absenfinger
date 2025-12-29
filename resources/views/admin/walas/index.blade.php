@extends('layouts.admin')

@section('title', 'Wali Kelas')
@section('page-title', 'Wali Kelas')

@section('content')
    <div class="space-y-6" x-data="{ 
        showAddModal: false,
        showEditModal: false,
        editData: { id: null, guru_id: '', kelas_id: '', tp_id: '', is_active: true }
    }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Wali Kelas</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data wali kelas</p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg shadow-purple-500/20 cursor-pointer">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Wali Kelas
            </button>
        </div>

        <!-- Stats Card -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg bg-purple-500/20 flex items-center justify-center">
                    <svg class="w-5 h-5 text-purple-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-2xl font-bold text-white">{{ $walasData->count() }}</p>
                    <p class="text-xs text-slate-400">Total Wali Kelas</p>
                </div>
            </div>
        </div>

        <!-- Search Box -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <form action="{{ route('admin.walas.index') }}" method="GET" class="flex items-center gap-3">
                <div class="relative w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari guru atau kelas..."
                        class="w-full px-3 pr-8 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50">
                    @if(request('search'))
                        <a href="{{ route('admin.walas.index') }}" class="absolute inset-y-0 right-0 pr-2 flex items-center cursor-pointer">
                            <svg class="w-4 h-4 text-slate-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
                <button type="submit" class="px-3 py-2 bg-purple-500/20 text-purple-400 rounded-lg hover:bg-purple-500/30 transition-colors cursor-pointer">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">NIP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Kelas</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Tahun Pelajaran</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($walasData as $index => $walas)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-white">{{ $walas->guru->nama ?? '-' }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $walas->guru->nip ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                        {{ $walas->kelas->nm_kls ?? '-' }}
                                    </span>
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">{{ $walas->tahunPelajaran->nm_tp ?? '-' }}</td>
                                <td class="px-4 py-3">
                                    @if($walas->is_active)
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Aktif</span>
                                    @else
                                        <span class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-slate-500/10 text-slate-400 border border-slate-500/20">Nonaktif</span>
                                    @endif
                                </td>
                                <td class="px-4 py-3">
                                    <div class="flex items-center justify-end gap-2">
                                        <button type="button" 
                                            @click="editData = { id: {{ $walas->id }}, guru_id: '{{ $walas->guru_id }}', kelas_id: '{{ $walas->kelas_id }}', tp_id: '{{ $walas->tp_id ?? '' }}', is_active: {{ $walas->is_active ? 'true' : 'false' }} }; showEditModal = true"
                                            class="p-2 bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 rounded-lg transition-colors cursor-pointer" title="Edit">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.walas.destroy', $walas) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 rounded-lg transition-colors cursor-pointer" title="Hapus">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    <p class="text-slate-400">Belum ada data wali kelas</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 max-h-[90vh] overflow-y-auto"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Tambah Wali Kelas</h3>
                        <p class="text-sm text-slate-400 mt-1">Pilih guru dan kelas</p>
                    </div>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form action="{{ route('admin.walas.store') }}" method="POST">
                    @csrf
                    <div class="space-y-5">
                        <!-- Guru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Guru <span class="text-rose-400">*</span></label>
                            <select name="guru_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                <option value="">-- Pilih Guru --</option>
                                @foreach($availableGurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }} {{ $guru->nip ? '('.$guru->nip.')' : '' }}</option>
                                @endforeach
                            </select>
                            @if($availableGurus->isEmpty())
                                <p class="text-xs text-amber-400 mt-1">Semua guru sudah menjadi wali kelas</p>
                            @endif
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas <span class="text-rose-400">*</span></label>
                            <select name="kelas_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($availableKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                            @if($availableKelas->isEmpty())
                                <p class="text-xs text-amber-400 mt-1">Semua kelas sudah memiliki wali kelas</p>
                            @endif
                        </div>

                        <!-- Tahun Pelajaran -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran</label>
                            <select name="tp_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                <option value="">-- Pilih Tahun Pelajaran --</option>
                                @foreach($tahunPelajarans as $tp)
                                    <option value="{{ $tp->id }}">{{ $tp->nm_tp }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div class="border-t border-slate-700/50 pt-4">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" checked
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-purple-500 focus:ring-purple-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium rounded-xl hover:from-purple-600 hover:to-pink-600 transition-all shadow-lg shadow-purple-500/20 cursor-pointer">
                                Simpan
                            </button>
                            <button type="button" @click="showAddModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center"
            @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-4xl m-4 max-h-[90vh] overflow-y-auto"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-white">Edit Wali Kelas</h3>
                        <p class="text-sm text-slate-400 mt-1">Perbarui data wali kelas</p>
                    </div>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>

                <form :action="'{{ route('admin.walas.index') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-5">
                        <!-- Guru -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Guru <span class="text-rose-400">*</span></label>
                            <select name="guru_id" x-model="editData.guru_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                @foreach($allGurus as $guru)
                                    <option value="{{ $guru->id }}">{{ $guru->nama }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Kelas -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Kelas <span class="text-rose-400">*</span></label>
                            <select name="kelas_id" x-model="editData.kelas_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                @foreach($allKelas as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Tahun Pelajaran -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Tahun Pelajaran</label>
                            <select name="tp_id" x-model="editData.tp_id"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-purple-500/50">
                                <option value="">-- Pilih Tahun Pelajaran --</option>
                                @foreach($tahunPelajarans as $tp)
                                    <option value="{{ $tp->id }}">{{ $tp->nm_tp }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status -->
                        <div>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1" x-bind:checked="editData.is_active"
                                    class="w-4 h-4 rounded border-slate-600 bg-slate-800 text-purple-500 focus:ring-purple-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-700/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20 cursor-pointer">
                                Update
                            </button>
                            <button type="button" @click="showEditModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                                Batal
                            </button>
                        </div>
                    </div>
                </form>
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
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg z-50">
            <ul class="text-sm list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
@endsection
