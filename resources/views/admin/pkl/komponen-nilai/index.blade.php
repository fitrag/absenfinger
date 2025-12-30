@extends('layouts.admin')

@section('title', 'Komponen Penilaian PKL')

@section('content')
    <div class="space-y-6" x-data="{ activeTab: '{{ request('tab', 'soft') }}' ,
                        searchSoft: '',
                        searchHard: '',
                        searchWirausaha: '',
                        perPageSoft: 10,
                        perPageHard: 10,
                        perPageWirausaha: 10,
                        get filteredSoftGroups() {
                            let groups = JSON.parse(this.$refs.softData.textContent || '{}');
                            if (this.searchSoft) {
                                let filtered = {};
                                for (let jurusan in groups) {
                                    if (jurusan.toLowerCase().includes(this.searchSoft.toLowerCase())) {
                                        filtered[jurusan] = groups[jurusan];
                                    }
                                }
                                return filtered;
                            }
                            return groups;
                        },
                        get filteredHardGroups() {
                            let groups = JSON.parse(this.$refs.hardData.textContent || '{}');
                            if (this.searchHard) {
                                let filtered = {};
                                for (let jurusan in groups) {
                                    if (jurusan.toLowerCase().includes(this.searchHard.toLowerCase())) {
                                        filtered[jurusan] = groups[jurusan];
                                    }
                                }
                                return filtered;
                            }
                            return groups;
                        },
                        get filteredWirausahaGroups() {
                            let groups = JSON.parse(this.$refs.wirausahaData.textContent || '{}');
                            if (this.searchWirausaha) {
                                let filtered = {};
                                for (let jurusan in groups) {
                                    if (jurusan.toLowerCase().includes(this.searchWirausaha.toLowerCase())) {
                                        filtered[jurusan] = groups[jurusan];
                                    }
                                }
                                return filtered;
                            }
                            return groups;
                        }
                    }">
        <!-- Hidden data containers -->
        <script type="application/json" x-ref="softData">
                            @php
                                $softGrouped = $kompSoftList->groupBy(function ($item) {
                                    return $item->jurusan->paket_keahlian ?? 'Tidak Ada Jurusan';
                                })->toArray();
                            @endphp
                            {!! json_encode($softGrouped) !!}
                        </script>
        <script type="application/json" x-ref="hardData">
                            @php
                                $hardGrouped = $kompHardList->groupBy(function ($item) {
                                    return $item->jurusan->paket_keahlian ?? 'Tidak Ada Jurusan';
                                })->toArray();
                            @endphp
                            {!! json_encode($hardGrouped) !!}
                        </script>
        <script type="application/json" x-ref="wirausahaData">
                            @php
                                $wirausahaGrouped = $kompWirausahaList->groupBy(function ($item) {
                                    return $item->jurusan->paket_keahlian ?? 'Tidak Ada Jurusan';
                                })->toArray();
                            @endphp
                            {!! json_encode($wirausahaGrouped) !!}
                        </script>

        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Komponen Penilaian PKL</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola komponen penilaian soft skill dan hard skill per jurusan</p>
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-emerald-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['totalSoft'] }}</p>
                        <p class="text-xs text-slate-400">Total Komponen Soft Skill</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-blue-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['totalHard'] }}</p>
                        <p class="text-xs text-slate-400">Total Komponen Hard Skill</p>
                    </div>
                </div>
            </div>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-5">
                <div class="flex items-center gap-3">
                    <div class="w-12 h-12 rounded-xl bg-amber-500/20 flex items-center justify-center">
                        <svg class="w-6 h-6 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div>
                        <p class="text-2xl font-bold text-white">{{ $stats['totalWirausaha'] }}</p>
                        <p class="text-xs text-slate-400">Total Komponen Wirausaha</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tabs -->
        <div class="flex gap-2 border-b border-slate-700/50 pb-2">
            <button @click="activeTab = 'soft'"
                :class="activeTab === 'soft' ? 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30' : 'text-slate-400 hover:text-white border-transparent'"
                class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all border cursor-pointer">
                Komponen Soft Skill
            </button>
            <button @click="activeTab = 'hard'"
                :class="activeTab === 'hard' ? 'bg-blue-500/20 text-blue-400 border-blue-500/30' : 'text-slate-400 hover:text-white border-transparent'"
                class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all border cursor-pointer">
                Komponen Hard Skill
            </button>
            <button @click="activeTab = 'wirausaha'"
                :class="activeTab === 'wirausaha' ? 'bg-amber-500/20 text-amber-400 border-amber-500/30' : 'text-slate-400 hover:text-white border-transparent'"
                class="px-4 py-2.5 rounded-xl text-sm font-medium transition-all border cursor-pointer">
                Komponen Wirausaha
            </button>
        </div>

        <!-- Soft Skill Section -->
        <div x-show="activeTab === 'soft'" x-transition>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-semibold text-emerald-400">Komponen Soft Skill</h3>
                    <div class="flex items-center gap-3">
                        <!-- Jurusan Filter -->
                        <select x-model="searchSoft"
                            class="px-4 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm w-52">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusanList as $jurusan)
                                <option value="{{ strtolower($jurusan->paket_keahlian) }}">{{ $jurusan->paket_keahlian }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Per Page -->
                        <select x-model="perPageSoft"
                            class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                            <option value="9999">Semua</option>
                        </select>
                        <button onclick="openModal('addSoftModal')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/25 cursor-pointer text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $softGrouped = $kompSoftList->groupBy(function ($item) {
                            return $item->jurusan->paket_keahlian ?? 'Tidak Ada Jurusan';
                        });
                    @endphp
                    @forelse($softGrouped as $jurusanName => $komponenList)
                        <div class="rounded-xl bg-slate-900/50 border border-slate-700/50 overflow-hidden"
                            x-show="!searchSoft || '{{ strtolower($jurusanName) }}'.includes(searchSoft.toLowerCase())">
                            <div
                                class="px-4 py-3 bg-emerald-500/10 border-b border-emerald-500/20 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-emerald-400">{{ $jurusanName }}</h4>
                                <span class="text-xs text-slate-400">{{ count($komponenList) }} komponen</span>
                            </div>
                            <div class="divide-y divide-slate-700/50">
                                @foreach($komponenList->take((int) request('per_page_soft', 9999)) as $index => $komponen)
                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-800/30 transition-colors"
                                        x-show="{{ $index }} < perPageSoft">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-slate-500 w-6">{{ $index + 1 }}.</span>
                                            <span class="text-sm text-white">{{ $komponen->nama }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button onclick="openEditSoftModal({{ json_encode($komponen) }})" title="Edit"
                                                class="p-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/40 rounded-lg transition-colors cursor-pointer border border-blue-500/30">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.pkl.komponen-nilai.destroySoft', $komponen->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                    class="p-1.5 bg-red-500/20 text-red-400 hover:bg-red-500/40 rounded-lg transition-colors cursor-pointer border border-red-500/30">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-slate-400">Belum ada data Komponen Soft Skill</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Hard Skill Section -->
        <div x-show="activeTab === 'hard'" x-transition>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-semibold text-blue-400">Komponen Hard Skill</h3>
                    <div class="flex items-center gap-3">
                        <!-- Jurusan Filter -->
                        <select x-model="searchHard"
                            class="px-4 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm w-52">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusanList as $jurusan)
                                <option value="{{ strtolower($jurusan->paket_keahlian) }}">{{ $jurusan->paket_keahlian }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Per Page -->
                        <select x-model="perPageHard"
                            class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                            <option value="9999">Semua</option>
                        </select>
                        <button onclick="openModal('addHardModal')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/25 cursor-pointer text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $hardGrouped = $kompHardList->groupBy(function ($item) {
                            return $item->jurusan->paket_keahlian ?? 'Tidak Ada Jurusan';
                        });
                    @endphp
                    @forelse($hardGrouped as $jurusanName => $komponenList)
                        <div class="rounded-xl bg-slate-900/50 border border-slate-700/50 overflow-hidden"
                            x-show="!searchHard || '{{ strtolower($jurusanName) }}'.includes(searchHard.toLowerCase())">
                            <div class="px-4 py-3 bg-blue-500/10 border-b border-blue-500/20 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-blue-400">{{ $jurusanName }}</h4>
                                <span class="text-xs text-slate-400">{{ count($komponenList) }} komponen</span>
                            </div>
                            <div class="divide-y divide-slate-700/50">
                                @foreach($komponenList->take((int) request('per_page_hard', 9999)) as $index => $komponen)
                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-800/30 transition-colors"
                                        x-show="{{ $index }} < perPageHard">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-slate-500 w-6">{{ $index + 1 }}.</span>
                                            <span class="text-sm text-white">{{ $komponen->nama }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button onclick="openEditHardModal({{ json_encode($komponen) }})" title="Edit"
                                                class="p-1.5 bg-blue-500/20 text-blue-400 hover:bg-blue-500/40 rounded-lg transition-colors cursor-pointer border border-blue-500/30">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.pkl.komponen-nilai.destroyHard', $komponen->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                    class="p-1.5 bg-red-500/20 text-red-400 hover:bg-red-500/40 rounded-lg transition-colors cursor-pointer border border-red-500/30">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                            </svg>
                            <p class="text-slate-400">Belum ada data Komponen Hard Skill</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Wirausaha Section -->
        <div x-show="activeTab === 'wirausaha'" x-transition>
            <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                <div
                    class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 p-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-semibold text-amber-400">Komponen Wirausaha</h3>
                    <div class="flex items-center gap-3">
                        <!-- Jurusan Filter -->
                        <select x-model="searchWirausaha"
                            class="px-4 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm w-52">
                            <option value="">Semua Jurusan</option>
                            @foreach($jurusanList as $jurusan)
                                <option value="{{ strtolower($jurusan->paket_keahlian) }}">{{ $jurusan->paket_keahlian }}
                                </option>
                            @endforeach
                        </select>
                        <!-- Per Page -->
                        <select x-model="perPageWirausaha"
                            class="px-3 py-2 bg-slate-900/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            <option value="10">10</option>
                            <option value="20">20</option>
                            <option value="30">30</option>
                            <option value="40">40</option>
                            <option value="50">50</option>
                            <option value="9999">Semua</option>
                        </select>
                        <button onclick="openModal('addWirausahaModal')"
                            class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/25 cursor-pointer text-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                            </svg>
                            Tambah
                        </button>
                    </div>
                </div>
                <div class="p-4 space-y-4">
                    @php
                        $wirausahaGrouped = $kompWirausahaList->groupBy(function ($item) {
                            return $item->jurusan->paket_keahlian ?? 'Tidak Ada Jurusan';
                        });
                    @endphp
                    @forelse($wirausahaGrouped as $jurusanName => $komponenList)
                        <div class="rounded-xl bg-slate-900/50 border border-slate-700/50 overflow-hidden"
                            x-show="!searchWirausaha || '{{ strtolower($jurusanName) }}'.includes(searchWirausaha.toLowerCase())">
                            <div
                                class="px-4 py-3 bg-amber-500/10 border-b border-amber-500/20 flex items-center justify-between">
                                <h4 class="text-sm font-semibold text-amber-400">{{ $jurusanName }}</h4>
                                <span class="text-xs text-slate-400">{{ count($komponenList) }} komponen</span>
                            </div>
                            <div class="divide-y divide-slate-700/50">
                                @foreach($komponenList->take((int) request('per_page_wirausaha', 9999)) as $index => $komponen)
                                    <div class="flex items-center justify-between px-4 py-2.5 hover:bg-slate-800/30 transition-colors"
                                        x-show="{{ $index }} < perPageWirausaha">
                                        <div class="flex items-center gap-3">
                                            <span class="text-xs text-slate-500 w-6">{{ $index + 1 }}.</span>
                                            <span class="text-sm text-white">{{ $komponen->nama }}</span>
                                        </div>
                                        <div class="flex items-center gap-1">
                                            <button onclick="openEditWirausahaModal({{ json_encode($komponen) }})" title="Edit"
                                                class="p-1.5 bg-amber-500/20 text-amber-400 hover:bg-amber-500/40 rounded-lg transition-colors cursor-pointer border border-amber-500/30">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                                </svg>
                                            </button>
                                            <form action="{{ route('admin.pkl.komponen-nilai.destroyWirausaha', $komponen->id) }}"
                                                method="POST" class="inline"
                                                onsubmit="return confirm('Yakin ingin menghapus data ini?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" title="Hapus"
                                                    class="p-1.5 bg-red-500/20 text-red-400 hover:bg-red-500/40 rounded-lg transition-colors cursor-pointer border border-red-500/30">
                                                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                    </svg>
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @empty
                        <div class="text-center py-12">
                            <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-slate-400">Belum ada data Komponen Wirausaha</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Add Soft Skill Modal -->
    <div id="addSoftModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addSoftModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-emerald-500/30 w-full max-w-4xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-emerald-400 mb-6">Tambah Komponen Soft Skill</h3>
                <form action="{{ route('admin.pkl.komponen-nilai.storeSoft') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jurusan <span
                                    class="text-red-400">*</span></label>
                            <select name="m_jurusan_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Jurusan</option>
                                <option value="all">-- Semua Jurusan --</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Komponen <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama" required placeholder="Masukkan nama komponen soft skill"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addSoftModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Soft Skill Modal -->
    <div id="editSoftModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editSoftModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-emerald-500/30 w-full max-w-4xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-emerald-400 mb-6">Edit Komponen Soft Skill</h3>
                <form id="editSoftForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jurusan <span
                                    class="text-red-400">*</span></label>
                            <select name="m_jurusan_id" id="editSoftJurusanId" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Komponen <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama" id="editSoftNama" required
                                placeholder="Masukkan nama komponen soft skill"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editSoftModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-medium rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Hard Skill Modal -->
    <div id="addHardModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addHardModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-blue-500/30 w-full max-w-4xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-blue-400 mb-6">Tambah Komponen Hard Skill</h3>
                <form action="{{ route('admin.pkl.komponen-nilai.storeHard') }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jurusan <span
                                    class="text-red-400">*</span></label>
                            <select name="m_jurusan_id" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Jurusan</option>
                                <option value="all">-- Semua Jurusan --</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Komponen <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama" required placeholder="Masukkan nama komponen hard skill"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('addHardModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Hard Skill Modal -->
    <div id="editHardModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editHardModal')"></div>
            <div class="relative bg-slate-900 rounded-2xl border border-blue-500/30 w-full max-w-4xl p-6 shadow-2xl">
                <h3 class="text-xl font-bold text-blue-400 mb-6">Edit Komponen Hard Skill</h3>
                <form id="editHardForm" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Jurusan <span
                                    class="text-red-400">*</span></label>
                            <select name="m_jurusan_id" id="editHardJurusanId" required
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                <option value="">Pilih Jurusan</option>
                                @foreach($jurusanList as $jurusan)
                                    <option value="{{ $jurusan->id }}">{{ $jurusan->paket_keahlian }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Nama Komponen <span
                                    class="text-red-400">*</span></label>
                            <input type="text" name="nama" id="editHardNama" required
                                placeholder="Masukkan nama komponen hard skill"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                        </div>
                    </div>
                    <div class="flex justify-end gap-3">
                        <button type="button" onclick="closeModal('editHardModal')"
                            class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Add Wirausaha Modal -->
        <div id="addWirausahaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('addWirausahaModal')"></div>
                <div class="relative bg-slate-900 rounded-2xl border border-amber-500/30 w-full max-w-4xl p-6 shadow-2xl">
                    <h3 class="text-xl font-bold text-amber-400 mb-6">Tambah Komponen Wirausaha</h3>
                    <form action="{{ route('admin.pkl.komponen-nilai.storeWirausaha') }}" method="POST">
                        @csrf
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jurusan <span
                                        class="text-red-400">*</span></label>
                                <select name="m_jurusan_id" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Jurusan</option>
                                    <option value="all">-- Semua Jurusan --</option>
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->paket_keahlian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Komponen <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="nama" required placeholder="Masukkan nama komponen wirausaha"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="closeModal('addWirausahaModal')"
                                class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all cursor-pointer">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Wirausaha Modal -->
        <div id="editWirausahaModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20">
                <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" onclick="closeModal('editWirausahaModal')"></div>
                <div class="relative bg-slate-900 rounded-2xl border border-amber-500/30 w-full max-w-4xl p-6 shadow-2xl">
                    <h3 class="text-xl font-bold text-amber-400 mb-6">Edit Komponen Wirausaha</h3>
                    <form id="editWirausahaForm" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="space-y-4 mb-6">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Jurusan <span
                                        class="text-red-400">*</span></label>
                                <select name="m_jurusan_id" id="editWirausahaJurusanId" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                                    <option value="">Pilih Jurusan</option>
                                    @foreach($jurusanList as $jurusan)
                                        <option value="{{ $jurusan->id }}">{{ $jurusan->paket_keahlian }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Nama Komponen <span
                                        class="text-red-400">*</span></label>
                                <input type="text" name="nama" id="editWirausahaNama" required
                                    placeholder="Masukkan nama komponen wirausaha"
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm">
                            </div>
                        </div>
                        <div class="flex justify-end gap-3">
                            <button type="button" onclick="closeModal('editWirausahaModal')"
                                class="px-4 py-2.5 text-slate-400 hover:text-white transition-colors cursor-pointer">Batal</button>
                            <button type="submit"
                                class="px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all cursor-pointer">Simpan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <script>
            function openModal(id) {
                document.getElementById(id).classList.remove('hidden');
            }
            function closeModal(id) {
                document.getElementById(id).classList.add('hidden');
            }
            function openEditSoftModal(komponen) {
                document.getElementById('editSoftForm').action = `/admin/pkl/komponen-nilai/soft/${komponen.id}`;
                document.getElementById('editSoftJurusanId').value = komponen.m_jurusan_id || '';
                document.getElementById('editSoftNama').value = komponen.nama || '';
                openModal('editSoftModal');
            }
            function openEditHardModal(komponen) {
                document.getElementById('editHardForm').action = `/admin/pkl/komponen-nilai/hard/${komponen.id}`;
                document.getElementById('editHardJurusanId').value = komponen.m_jurusan_id || '';
                document.getElementById('editHardNama').value = komponen.nama || '';
                openModal('editHardModal');
            }
            function openEditWirausahaModal(komponen) {
                document.getElementById('editWirausahaForm').action = `/admin/pkl/komponen-nilai/wirausaha/${komponen.id}`;
                document.getElementById('editWirausahaJurusanId').value = komponen.m_jurusan_id || '';
                document.getElementById('editWirausahaNama').value = komponen.nama || '';
                openModal('editWirausahaModal');
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
@endsection