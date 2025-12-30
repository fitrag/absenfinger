@extends('layouts.admin')

@section('title', 'Dashboard PKL')

@section('page-title', 'Absen PKL')

@section('content')
    <div class="space-y-6" x-data="pklAttendance()">
        <!-- Welcome Card -->
        <div class="rounded-2xl bg-gradient-to-br from-blue-600 to-purple-700 p-6 shadow-xl">
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-blue-100 text-sm">Selamat datang,</p>
                    <h2 class="text-2xl font-bold text-white mt-1">{{ $student->name }}</h2>
                    <p class="text-blue-200 text-sm mt-1">NIS: {{ $student->nis }}</p>
                </div>
                <div class="w-14 h-14 rounded-xl bg-white/20 backdrop-blur flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- DUDI Info -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-5">
            <h3 class="text-sm font-medium text-slate-400 mb-3">Tempat PKL</h3>
            <div class="space-y-3">
                <div class="flex items-start gap-3">
                    <div class="w-10 h-10 rounded-lg bg-emerald-500/20 flex items-center justify-center flex-shrink-0">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                        </svg>
                    </div>
                    <div>
                        <p class="font-semibold text-white">{{ $dudi->nama }}</p>
                        <p class="text-sm text-slate-400 mt-0.5">{{ $dudi->alamat ?? '-' }}</p>
                        @if($dudi->bidang_usaha)
                            <span class="inline-block mt-2 px-2 py-0.5 bg-emerald-500/20 text-emerald-400 text-xs rounded-full">
                                {{ $dudi->bidang_usaha }}
                            </span>
                        @endif
                    </div>
                </div>
                @if($pkl->pembimbingSekolah)
                    <div class="pt-3 border-t border-slate-800/50">
                        <p class="text-xs text-slate-500">Pembimbing Sekolah</p>
                        <p class="text-sm text-slate-300">{{ $pkl->pembimbingSekolah->nama }}</p>
                    </div>
                @endif
            </div>
        </div>

        <!-- Stats -->
        <div class="grid grid-cols-2 gap-4">
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-white">{{ $monthlyAttendance }}</p>
                <p class="text-xs text-slate-400 mt-1">Kehadiran Bulan Ini</p>
            </div>
            <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4 text-center">
                <p class="text-3xl font-bold text-white">{{ now()->format('d M') }}</p>
                <p class="text-xs text-slate-400 mt-1">Hari Ini</p>
            </div>
        </div>

        <!-- Today's Attendance Status -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-5">
            <h3 class="text-sm font-medium text-slate-400 mb-4">Status Absensi Hari Ini</h3>
            <div class="grid grid-cols-2 gap-4">
                <!-- Check In Status -->
                <div
                    class="rounded-xl p-4 {{ $todayCheckIn ? 'bg-emerald-500/10 border border-emerald-500/30' : 'bg-slate-800/50 border border-slate-700/50' }}">
                    <div class="flex items-center gap-2 mb-2">
                        @if($todayCheckIn)
                            <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-emerald-400">Sudah Masuk</span>
                        @else
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-slate-400">Belum Masuk</span>
                        @endif
                    </div>
                    @if($todayCheckIn)
                        <p class="text-2xl font-bold text-white">{{ $todayCheckIn->checktime->format('H:i') }}</p>
                    @else
                        <p class="text-2xl font-bold text-slate-600">--:--</p>
                    @endif
                </div>

                <!-- Check Out Status -->
                <div
                    class="rounded-xl p-4 {{ $todayCheckOut ? 'bg-blue-500/10 border border-blue-500/30' : 'bg-slate-800/50 border border-slate-700/50' }}">
                    <div class="flex items-center gap-2 mb-2">
                        @if($todayCheckOut)
                            <svg class="w-5 h-5 text-blue-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                            </svg>
                            <span class="text-sm font-medium text-blue-400">Sudah Pulang</span>
                        @else
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-medium text-slate-400">Belum Pulang</span>
                        @endif
                    </div>
                    @if($todayCheckOut)
                        <p class="text-2xl font-bold text-white">{{ $todayCheckOut->checktime->format('H:i') }}</p>
                    @else
                        <p class="text-2xl font-bold text-slate-600">--:--</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Attendance Actions -->
        <div class="space-y-3">
            @if(!$todayCheckIn)
                <!-- Check In Button -->
                <button @click="doCheckIn()" :disabled="isLoading"
                    class="w-full py-4 px-6 bg-gradient-to-r from-emerald-500 to-teal-500 text-white font-semibold rounded-xl hover:from-emerald-600 hover:to-teal-600 transition-all shadow-lg shadow-emerald-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                    <template x-if="!isLoading">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1" />
                        </svg>
                    </template>
                    <template x-if="isLoading">
                        <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </template>
                    <span x-text="isLoading ? 'Memproses...' : 'Absen Masuk'"></span>
                </button>
            @elseif(!$todayCheckOut)
                <!-- Check Out Button -->
                <button @click="doCheckOut()" :disabled="isLoading"
                    class="w-full py-4 px-6 bg-gradient-to-r from-blue-500 to-indigo-500 text-white font-semibold rounded-xl hover:from-blue-600 hover:to-indigo-600 transition-all shadow-lg shadow-blue-500/30 disabled:opacity-50 disabled:cursor-not-allowed flex items-center justify-center gap-3">
                    <template x-if="!isLoading">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </template>
                    <template x-if="isLoading">
                        <svg class="w-6 h-6 animate-spin" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor"
                                d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                            </path>
                        </svg>
                    </template>
                    <span x-text="isLoading ? 'Memproses...' : 'Absen Pulang'"></span>
                </button>
            @else
                <!-- Completed -->
                <div
                    class="w-full py-4 px-6 bg-slate-800/50 border border-slate-700/50 text-slate-400 font-medium rounded-xl text-center">
                    <div class="flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span>Absensi Hari Ini Selesai</span>
                    </div>
                </div>
            @endif
        </div>

        <!-- Location Status -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-lg flex items-center justify-center"
                    :class="locationStatus === 'granted' ? 'bg-emerald-500/20' : locationStatus === 'denied' ? 'bg-red-500/20' : 'bg-amber-500/20'">
                    <svg class="w-5 h-5"
                        :class="locationStatus === 'granted' ? 'text-emerald-400' : locationStatus === 'denied' ? 'text-red-400' : 'text-amber-400'"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-medium"
                        :class="locationStatus === 'granted' ? 'text-emerald-400' : locationStatus === 'denied' ? 'text-red-400' : 'text-amber-400'"
                        x-text="locationStatus === 'granted' ? 'Lokasi Aktif' : locationStatus === 'denied' ? 'Lokasi Ditolak' : 'Menunggu Izin Lokasi'">
                    </p>
                    <p class="text-xs text-slate-500" x-show="currentLocation">
                        <span x-text="currentLocation"></span>
                    </p>
                </div>
            </div>
        </div>

        <!-- Toast Notification -->
        <div x-show="toast.show" x-transition
            class="fixed bottom-4 left-4 right-4 max-w-md mx-auto px-4 py-3 rounded-xl shadow-xl z-50"
            :class="toast.type === 'success' ? 'bg-emerald-500' : 'bg-red-500'">
            <div class="flex items-center gap-3 text-white">
                <template x-if="toast.type === 'success'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </template>
                <template x-if="toast.type === 'error'">
                    <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </template>
                <p class="text-sm font-medium" x-text="toast.message"></p>
            </div>
        </div>
    </div>

    <script>
        function pklAttendance() {
            return {
                isLoading: false,
                locationStatus: 'pending',
                currentLocation: null,
                toast: {
                    show: false,
                    type: 'success',
                    message: ''
                },

                init() {
                    this.checkLocationPermission();
                },

                async checkLocationPermission() {
                    if (!navigator.geolocation) {
                        this.locationStatus = 'denied';
                        return;
                    }

                    try {
                        const permission = await navigator.permissions.query({ name: 'geolocation' });
                        this.locationStatus = permission.state;

                        permission.addEventListener('change', () => {
                            this.locationStatus = permission.state;
                        });
                    } catch (e) {
                        this.locationStatus = 'pending';
                    }
                },

                getLocation() {
                    return new Promise((resolve, reject) => {
                        if (!navigator.geolocation) {
                            reject(new Error('Geolocation tidak didukung browser Anda'));
                            return;
                        }

                        navigator.geolocation.getCurrentPosition(
                            (position) => {
                                this.locationStatus = 'granted';
                                this.currentLocation = `${position.coords.latitude.toFixed(6)}, ${position.coords.longitude.toFixed(6)}`;
                                resolve({
                                    latitude: position.coords.latitude,
                                    longitude: position.coords.longitude
                                });
                            },
                            (error) => {
                                this.locationStatus = 'denied';
                                let message = 'Gagal mendapatkan lokasi';
                                switch (error.code) {
                                    case error.PERMISSION_DENIED:
                                        message = 'Izin lokasi ditolak. Aktifkan lokasi di pengaturan browser.';
                                        break;
                                    case error.POSITION_UNAVAILABLE:
                                        message = 'Lokasi tidak tersedia';
                                        break;
                                    case error.TIMEOUT:
                                        message = 'Timeout mendapatkan lokasi';
                                        break;
                                }
                                reject(new Error(message));
                            },
                            {
                                enableHighAccuracy: true,
                                timeout: 10000,
                                maximumAge: 0
                            }
                        );
                    });
                },

                showToast(type, message) {
                    this.toast = { show: true, type, message };
                    setTimeout(() => {
                        this.toast.show = false;
                    }, 5000);
                },

                async doCheckIn() {
                    if (this.isLoading) return;
                    this.isLoading = true;

                    try {
                        const coords = await this.getLocation();

                        const response = await fetch('{{ route("admin.siswa.pkl.checkIn") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(coords)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showToast('success', data.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.showToast('error', data.message);
                        }
                    } catch (error) {
                        this.showToast('error', error.message);
                    } finally {
                        this.isLoading = false;
                    }
                },

                async doCheckOut() {
                    if (this.isLoading) return;
                    this.isLoading = true;

                    try {
                        const coords = await this.getLocation();

                        const response = await fetch('{{ route("admin.siswa.pkl.checkOut") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(coords)
                        });

                        const data = await response.json();

                        if (data.success) {
                            this.showToast('success', data.message);
                            setTimeout(() => window.location.reload(), 1500);
                        } else {
                            this.showToast('error', data.message);
                        }
                    } catch (error) {
                        this.showToast('error', error.message);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
@endsection