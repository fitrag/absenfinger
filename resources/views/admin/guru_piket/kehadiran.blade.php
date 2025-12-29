@extends('layouts.admin')

@section('title', 'Kehadiran Guru')
@section('page-title', 'Kehadiran Guru')

@section('content')
    <div class="space-y-6" x-data="{ 
            showAddModal: false,
            showEditModal: false,
            editData: { id: null, guru_id: '', date: '{{ $date }}', status: 'hadir', check_in: '', check_out: '', notes: '' },
            search: '{{ request('search') }}'
        }">
        
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Kehadiran Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data kehadiran guru harian</p>
            </div>
            <button @click="showAddModal = true; editData = { id: null, guru_id: '', date: '{{ $date }}', status: 'hadir', check_in: '', check_out: '', notes: '' }"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-cyan-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-cyan-600 transition-all shadow-lg shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah Kehadiran
            </button>
        </div>

        <!-- Stats Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <!-- Hadir -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['hadir'] }}</p>
                        <p class="text-xs text-slate-400">Hadir</p>
                    </div>
                </div>
            </div>
            
            <!-- Sakit/Izin -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['sakit'] + $stats['izin'] }}</p>
                        <p class="text-xs text-slate-400">Sakit / Izin</p>
                    </div>
                </div>
            </div>

            <!-- Alpha -->
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-rose-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['alpha'] }}</p>
                        <p class="text-xs text-slate-400">Alpha</p>
                    </div>
                </div>
            </div>

             <!-- Total / Belum Absen -->
             <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-lg bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['belum_absen'] }} <span class="text-sm font-normal text-slate-500">/ {{ $stats['total'] }}</span></p>
                        <p class="text-xs text-slate-400">Belum Absen</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filter & Search -->
        <div class="flex flex-wrap items-center justify-between gap-4">
             <form action="{{ route('admin.guru-piket.kehadiran') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
                <div class="relative w-full sm:w-48">
                     <input type="date" name="date" value="{{ $date }}" 
                        class="w-full px-4 py-2 bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
                        onchange="this.form.submit()">
                </div>
            </form>

            <form action="{{ route('admin.guru-piket.kehadiran') }}" method="GET" class="flex items-center gap-3 w-full sm:w-auto">
                <input type="hidden" name="date" value="{{ $date }}">
                <div class="relative w-full sm:w-64">
                    <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari guru..."
                        class="w-full px-3 pr-8 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    @if(request('search'))
                        <a href="{{ route('admin.guru-piket.kehadiran', ['date' => $date]) }}" class="absolute inset-y-0 right-0 pr-2 flex items-center">
                            <svg class="w-4 h-4 text-slate-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
                <button type="submit" class="px-3 py-2 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
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
                        <tr class="border-b border-slate-800/50 bg-slate-800/20">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Guru</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">NIP</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Status</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Waktu</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($gurus as $index => $guru)
                            @php
                                $attendance = $guru->attendances->first();
                            @endphp
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-sm text-slate-400">{{ $loop->iteration }}</td>
                                <td class="px-4 py-3 text-sm font-medium text-white">{{ $guru->nama }}</td>
                                <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $guru->nip ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($attendance)
                                        @php
                                            $colors = [
                                                'hadir' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/20',
                                                'sakit' => 'bg-amber-500/10 text-amber-400 border-amber-500/20',
                                                'izin' => 'bg-blue-500/10 text-blue-400 border-blue-500/20',
                                                'alpha' => 'bg-rose-500/10 text-rose-400 border-rose-500/20',
                                            ];
                                            $color = $colors[$attendance->status] ?? 'bg-slate-500/10 text-slate-400';
                                        @endphp
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium border {{ $color }}">
                                            {{ ucfirst($attendance->status) }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-slate-700 text-slate-400">
                                            Belum Absen
                                        </span>
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-300">
                                    @if($attendance)
                                        <div class="flex flex-col text-xs">
                                            <span>In: {{ $attendance->check_in ? \Carbon\Carbon::parse($attendance->check_in)->format('H:i') : '-' }}</span>
                                            <span>Out: {{ $attendance->check_out ? \Carbon\Carbon::parse($attendance->check_out)->format('H:i') : '-' }}</span>
                                        </div>
                                    @else
                                        -
                                    @endif
                                </td>
                                <td class="px-4 py-3 text-sm text-slate-400 truncate max-w-xs">
                                    {{ $attendance->notes ?? '-' }}
                                </td>
                                <td class="px-4 py-3 text-right">
                                    @if($attendance)
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="editData = { 
                                                    id: {{ $attendance->id }}, 
                                                    guru_id: '{{ $guru->id }}', 
                                                    date: '{{ $date }}', 
                                                    status: '{{ $attendance->status }}', 
                                                    check_in: '{{ $attendance->check_in }}', 
                                                    check_out: '{{ $attendance->check_out }}', 
                                                    notes: '{{ addslashes($attendance->notes ?? '') }}' 
                                                }; showEditModal = true"
                                                class="text-amber-400 hover:text-amber-300 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.guru-piket.kehadiran.destroy', $attendance->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus data kehadiran ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-rose-400 hover:text-rose-300 transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    @else
                                        <button @click="editData = { id: null, guru_id: '{{ $guru->id }}', date: '{{ $date }}', status: 'hadir', check_in: '', check_out: '', notes: '' }; showAddModal = true"
                                            class="text-blue-400 hover:text-blue-300 text-xs font-medium transition-colors border border-blue-500/30 px-2 py-1 rounded-lg hover:bg-blue-500/10">
                                            + Input
                                        </button>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="px-4 py-12 text-center">
                                    <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                                    </svg>
                                    <p class="text-slate-400">Tidak ada data guru.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center" @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-lg m-4" @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Tambah Kehadiran</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form action="{{ route('admin.guru-piket.kehadiran.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="date" :value="editData.date">
                    <div class="space-y-4">
                        <!-- Guru Select -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Guru</label>
                            <select name="guru_id" x-model="editData.guru_id" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500" required>
                                <option value="">-- Pilih Guru --</option>
                                @foreach($gurus as $g)
                                    <option value="{{ $g->id }}">{{ $g->nama }}</option>
                                @endforeach
                            </select>
                        </div>
                        
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Status</label>
                            <div class="flex gap-4">
                                @foreach(['hadir', 'sakit', 'izin', 'alpha'] as $s)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="{{ $s }}" x-model="editData.status" class="text-blue-500 focus:ring-blue-500 bg-slate-800 border-slate-700">
                                        <span class="text-sm text-slate-300 capitalize">{{ $s }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Check In</label>
                                <input type="time" name="check_in" x-model="editData.check_in" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Check Out</label>
                                <input type="time" name="check_out" x-model="editData.check_out" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Catatan</label>
                            <textarea name="notes" x-model="editData.notes" rows="3" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="showAddModal = false" class="px-4 py-2 text-slate-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl">Simpan</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-center justify-center" @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-lg m-4" @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-white">Edit Kehadiran</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg></button>
                </div>
                <form :action="'{{ route('admin.guru-piket.kehadiran') }}/' + editData.id" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4">
                        <!-- Status -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Status</label>
                            <div class="flex gap-4">
                                @foreach(['hadir', 'sakit', 'izin', 'alpha'] as $s)
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="radio" name="status" value="{{ $s }}" x-model="editData.status" class="text-blue-500 focus:ring-blue-500 bg-slate-800 border-slate-700">
                                        <span class="text-sm text-slate-300 capitalize">{{ $s }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <!-- Time -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Check In</label>
                                <input type="time" name="check_in" x-model="editData.check_in" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-1">Check Out</label>
                                <input type="time" name="check_out" x-model="editData.check_out" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500">
                            </div>
                        </div>

                        <!-- Notes -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-1">Catatan</label>
                            <textarea name="notes" x-model="editData.notes" rows="3" class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white focus:border-blue-500"></textarea>
                        </div>

                        <div class="flex justify-end gap-3 pt-4 border-t border-slate-800">
                            <button type="button" @click="showEditModal = false" class="px-4 py-2 text-slate-400 hover:text-white">Batal</button>
                            <button type="submit" class="px-4 py-2 bg-blue-600 hover:bg-blue-500 text-white rounded-xl">Update</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

    </div>
@endsection