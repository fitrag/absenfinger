@extends('layouts.admin')

@section('title', 'Set Lokasi DUDI')

@section('page-title', 'Set Lokasi DUDI')

@section('content')
    <div class="space-y-6" x-data="lokasiPage()">
        <!-- Header -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl p-4 sm:p-6">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                <div>
                    <h2 class="text-xl font-bold text-white">Set Lokasi DUDI</h2>
                    <p class="text-slate-400 text-sm mt-1">
                        Atur koordinat lokasi dan radius absensi untuk tempat PKL siswa bimbingan Anda
                    </p>
                </div>
            </div>
        </div>

        <!-- DUDI List -->
        <div class="bg-slate-900/50 backdrop-blur-xl border border-slate-800/50 rounded-2xl overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead>
                        <tr class="bg-slate-800/30 border-b border-slate-700/50 text-left">
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider w-16">No</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama DUDI</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider">Alamat</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Lokasi</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-center">Radius</th>
                            <th class="px-6 py-4 text-xs font-semibold text-slate-400 uppercase tracking-wider text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-700/50">
                        @forelse($dudiList as $index => $dudi)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-6 py-4 text-sm text-slate-500">{{ $loop->iteration }}</td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="p-2 bg-blue-500/10 rounded-lg">
                                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-medium text-white">{{ $dudi->nama }}</p>
                                            <p class="text-xs text-slate-400">{{ $dudi->bidang_usaha ?? '-' }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-300 max-w-xs truncate">
                                    {{ $dudi->alamat ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    @if($dudi->latitude && $dudi->longitude)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/>
                                            </svg>
                                            Sudah Diset
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-medium bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                            <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                            </svg>
                                            Belum Diset
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 text-center text-sm text-slate-300">
                                    {{ $dudi->radius ?? 100 }} m
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button type="button"
                                        @click="openEditModal({{ json_encode($dudi) }})"
                                        class="px-3 py-1.5 rounded-lg text-xs font-medium transition-colors cursor-pointer inline-flex items-center gap-1.5 bg-blue-500/10 text-blue-400 border border-blue-500/30 hover:bg-blue-500/20">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        Set Lokasi
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center">
                                        <div class="w-16 h-16 rounded-full bg-slate-800/50 flex items-center justify-center mb-4">
                                            <svg class="w-8 h-8 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                            </svg>
                                        </div>
                                        <h3 class="text-sm font-medium text-white mb-1">Belum ada data DUDI</h3>
                                        <p class="text-xs text-slate-400 max-w-sm mx-auto">
                                            Belum ada DUDI yang terkait dengan siswa bimbingan PKL Anda.
                                        </p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Edit Location Modal -->
        <div x-show="showEditModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true"
            @keydown.escape.window="showEditModal = false">

            <!-- Backdrop -->
            <div x-show="showEditModal" x-transition:enter="transition-opacity ease-out duration-300"
                x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
                x-transition:leave="transition-opacity ease-in duration-200" x-transition:leave-start="opacity-100"
                x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/60 backdrop-blur-sm"
                @click="showEditModal = false">
            </div>

            <!-- Modal Panel -->
            <div class="flex items-start justify-center min-h-screen pt-10 pb-10 px-4">
                <div x-show="showEditModal" x-transition:enter="transition ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave="transition ease-in duration-200"
                    x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                    x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                    class="w-full max-w-2xl bg-slate-900 rounded-2xl border border-slate-800 shadow-2xl relative pointer-events-auto"
                    @click.stop>

                    <!-- Modal Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-700/50">
                        <div>
                            <h3 class="text-lg font-bold text-white">Set Lokasi DUDI</h3>
                            <p class="text-sm text-slate-400" x-text="editDudi.nama"></p>
                        </div>
                        <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <!-- Modal Body -->
                    <form :action="'/admin/guru/pkl/update-lokasi/' + editDudi.id" method="POST" class="p-6 space-y-6">
                        @csrf
                        @method('PUT')

                        <!-- Map Container -->
                        <div class="rounded-xl overflow-hidden border border-slate-700/50">
                            <div id="map" class="h-64 w-full bg-slate-800"></div>
                        </div>

                        <!-- Get Current Location Button -->
                        <button type="button" @click="getCurrentLocation()"
                            class="w-full px-4 py-3 bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 font-medium rounded-xl hover:bg-emerald-500/20 transition-colors cursor-pointer flex items-center justify-center gap-2">
                            <svg x-show="!gettingLocation" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg x-show="gettingLocation" class="w-5 h-5 animate-spin" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                            <span x-text="gettingLocation ? 'Mendapatkan Lokasi...' : 'Gunakan Lokasi Saat Ini'"></span>
                        </button>

                        <!-- Coordinates Input -->
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Latitude *</label>
                                <input type="text" name="latitude" x-model="editDudi.latitude" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="-6.123456">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-300 mb-2">Longitude *</label>
                                <input type="text" name="longitude" x-model="editDudi.longitude" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                    placeholder="106.123456">
                            </div>
                        </div>

                        <!-- Radius Input -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Radius Absensi (meter) *</label>
                            <input type="number" name="radius" x-model="editDudi.radius" required min="10" max="1000"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"
                                placeholder="100">
                            <p class="text-xs text-slate-500 mt-1">Jarak maksimal dari titik lokasi untuk absensi (10-1000 meter)</p>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 pt-4 border-t border-slate-700/50">
                            <button type="button" @click="showEditModal = false"
                                class="flex-1 px-4 py-3 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-all cursor-pointer">
                                Batal
                            </button>
                            <button type="submit"
                                class="flex-1 px-4 py-3 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                                Simpan Lokasi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Leaflet CSS & JS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>

    <script>
        function lokasiPage() {
            return {
                showEditModal: false,
                gettingLocation: false,
                editDudi: {
                    id: null,
                    nama: '',
                    latitude: '',
                    longitude: '',
                    radius: 100
                },
                map: null,
                marker: null,
                circle: null,

                openEditModal(dudi) {
                    this.editDudi = {
                        id: dudi.id,
                        nama: dudi.nama,
                        latitude: dudi.latitude || '',
                        longitude: dudi.longitude || '',
                        radius: dudi.radius || 100
                    };
                    this.showEditModal = true;

                    // Initialize map after modal is shown
                    this.$nextTick(() => {
                        setTimeout(() => this.initMap(), 100);
                    });
                },

                initMap() {
                    const lat = parseFloat(this.editDudi.latitude) || -6.200000;
                    const lng = parseFloat(this.editDudi.longitude) || 106.816666;
                    const radius = parseInt(this.editDudi.radius) || 100;

                    // Destroy existing map if any
                    if (this.map) {
                        this.map.remove();
                    }

                    // Create map
                    this.map = L.map('map').setView([lat, lng], 16);

                    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                        attribution: '© OpenStreetMap contributors'
                    }).addTo(this.map);

                    // Add marker
                    this.marker = L.marker([lat, lng], {
                        draggable: true
                    }).addTo(this.map);

                    // Add circle for radius
                    this.circle = L.circle([lat, lng], {
                        color: '#3b82f6',
                        fillColor: '#3b82f6',
                        fillOpacity: 0.2,
                        radius: radius
                    }).addTo(this.map);

                    // Update coordinates on marker drag
                    this.marker.on('dragend', (e) => {
                        const pos = e.target.getLatLng();
                        this.editDudi.latitude = pos.lat.toFixed(8);
                        this.editDudi.longitude = pos.lng.toFixed(8);
                        this.circle.setLatLng(pos);
                    });

                    // Update circle radius when input changes
                    this.$watch('editDudi.radius', (value) => {
                        if (this.circle && value) {
                            this.circle.setRadius(parseInt(value));
                        }
                    });
                },

                getCurrentLocation() {
                    if (!navigator.geolocation) {
                        alert('Geolocation tidak didukung oleh browser Anda.');
                        return;
                    }

                    this.gettingLocation = true;

                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const lat = position.coords.latitude;
                            const lng = position.coords.longitude;

                            this.editDudi.latitude = lat.toFixed(8);
                            this.editDudi.longitude = lng.toFixed(8);

                            // Update map
                            if (this.map && this.marker && this.circle) {
                                this.map.setView([lat, lng], 16);
                                this.marker.setLatLng([lat, lng]);
                                this.circle.setLatLng([lat, lng]);
                            }

                            this.gettingLocation = false;
                        },
                        (error) => {
                            this.gettingLocation = false;
                            let msg = 'Gagal mendapatkan lokasi.';
                            switch(error.code) {
                                case error.PERMISSION_DENIED:
                                    msg = 'Izin lokasi ditolak. Mohon izinkan akses lokasi.';
                                    break;
                                case error.POSITION_UNAVAILABLE:
                                    msg = 'Informasi lokasi tidak tersedia.';
                                    break;
                                case error.TIMEOUT:
                                    msg = 'Waktu permintaan lokasi habis.';
                                    break;
                            }
                            alert(msg);
                        },
                        {
                            enableHighAccuracy: true,
                            timeout: 10000,
                            maximumAge: 0
                        }
                    );
                }
            };
        }
    </script>

    <!-- Flash Messages -->
    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-rose-500 text-white rounded-xl shadow-lg flex items-center gap-2 z-50">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
            </svg>
            {{ session('error') }}
        </div>
    @endif
@endsection
