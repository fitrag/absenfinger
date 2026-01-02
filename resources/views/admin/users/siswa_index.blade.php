@extends('layouts.admin')

@section('title', 'Data User Siswa')
@section('page-title', 'Data User Siswa')

@section('content')
    <div class="space-y-6" x-data="statusModal()">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data User Siswa</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data user level siswa</p>
            </div>
            <div class="flex items-center gap-2">
                <button @click="showStatusModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-amber-500/20 text-amber-400 border border-amber-500/30 font-medium rounded-xl hover:bg-amber-500/30 transition-all">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    Update Status
                </button>
                <button @click="showModal = true"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah User
                </button>
            </div>
        </div>

        <!-- Filters -->
        <div class="flex flex-wrap items-center justify-between gap-4">
            <!-- Kelas Filter -->
            <div class="flex items-center gap-2">
                <label class="text-sm font-medium text-slate-300 whitespace-nowrap">Kelas</label>
                <select
                    onchange="window.location.href='{{ route('admin.users.siswa') }}?kelas_id=' + this.value + '&perPage={{ request('perPage', 36) }}'"
                    class="px-3 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    <option value="">Semua Kelas</option>
                    @foreach($kelasList as $kelas)
                        <option value="{{ $kelas->id }}" {{ request('kelas_id') == $kelas->id ? 'selected' : '' }}>
                            {{ $kelas->nm_kls }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Tampilkan -->
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-300 whitespace-nowrap">Tampilkan</label>
                <select
                    class="px-3 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
                    onchange="window.location.href='{{ route('admin.users.siswa') }}?perPage=' + this.value + '&kelas_id={{ request('kelas_id') }}'">
                    <option value="36" {{ request('perPage', 36) == 36 ? 'selected' : '' }}>36</option>
                    <option value="72" {{ request('perPage') == 72 ? 'selected' : '' }}>72</option>
                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ request('perPage') == 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div id="users-table-container">
            @include('admin.users.partials.siswa_table')
        </div>

        <!-- Add Modal -->
        <div x-show="showModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200"
            x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0"
            x-init="$watch('showModal', value => document.body.classList.toggle('overflow-hidden', value))"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showModal = false" style="display: none;">

            <div x-show="showModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="w-full max-w-2xl my-8">

                <div class="rounded-2xl bg-slate-900 border border-slate-800/50 shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/50 flex-shrink-0">
                        <h3 class="text-lg font-bold text-white">Tambah User Siswa</h3>
                        <button @click="showModal = false" class="p-1 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data"
                        class="p-6 space-y-4 overflow-y-auto">
                        @csrf
                        <!-- Hidden Level Input -->
                        <input type="hidden" name="level" value="siswa">

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nama <span
                                        class="text-rose-400">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                @error('name')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username <span
                                        class="text-rose-400">*</span></label>
                                <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                @error('username')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password <span
                                        class="text-rose-400">*</span></label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                @error('password')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <!-- Roles -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($roles as $role)
                                        <label
                                            class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-slate-800/30 hover:bg-slate-800/50 transition-colors">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
                                                class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-purple-500 focus:ring-purple-500/50">
                                            <span class="text-sm text-slate-300">{{ $role->nama_role }}</span>
                                        </label>
                                    @endforeach
                                </div>
                                @error('roles')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                        </div>
                        <!-- Foto -->
                        <div class="pt-4 pb-2">
                            <label for="foto" class="block text-sm font-medium text-slate-300 mb-2">Foto</label>
                            <input type="file" id="foto" name="foto" accept="image/*"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30">
                            @error('foto')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                        </div>
                        <!-- Boolean Checkboxes -->
                        <div class="pt-2">
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="checkbox" name="is_active" value="1"
                                    class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>
                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                            <button type="submit"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">Simpan</button>
                            <button type="button" @click="showModal = false"
                                class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">Batal</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Update Status Modal -->
        <div x-show="showStatusModal" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
            x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm overflow-y-auto"
            @click.self="showStatusModal = false" style="display: none;">

            <div x-show="showStatusModal" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95" class="w-full max-w-4xl my-8">

                <div class="rounded-2xl bg-slate-900 border border-slate-800/50 shadow-2xl overflow-hidden">
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800/50 flex-shrink-0">
                        <h3 class="text-lg font-bold text-white">Update Status User Siswa</h3>
                        <button @click="showStatusModal = false"
                            class="p-1 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.users.siswa.bulkStatus') }}" method="POST" class="p-6 space-y-4">
                        @csrf

                        <!-- Kelas Selection -->
                        <div>
                            <label class="block text-sm font-medium text-slate-300 mb-2">Pilih Kelas <span
                                    class="text-rose-400">*</span></label>
                            <select x-model="selectedKelas" @change="fetchUsersByKelas()"
                                class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                <option value="">-- Pilih Kelas --</option>
                                @foreach($kelasList as $kelas)
                                    <option value="{{ $kelas->id }}">{{ $kelas->nm_kls }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Status Selection -->
                        <div class="flex items-center gap-6 p-4 bg-slate-800/30 rounded-xl">
                            <span class="text-sm font-medium text-slate-300">Set Status:</span>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="active" checked
                                    class="w-4 h-4 text-emerald-500 bg-slate-800 border-slate-700 focus:ring-emerald-500/50">
                                <span class="text-sm text-emerald-400">Aktif</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer">
                                <input type="radio" name="status" value="inactive"
                                    class="w-4 h-4 text-rose-500 bg-slate-800 border-slate-700 focus:ring-rose-500/50">
                                <span class="text-sm text-rose-400">Nonaktif</span>
                            </label>
                        </div>

                        <!-- Loading State -->
                        <div x-show="loadingUsers" class="flex items-center justify-center py-8">
                            <svg class="w-8 h-8 animate-spin text-blue-500" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                                </circle>
                                <path class="opacity-75" fill="currentColor"
                                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                </path>
                            </svg>
                        </div>

                        <!-- Students List (show after kelas selected) -->
                        <template x-if="selectedKelas && !loadingUsers && usersList.length > 0">
                            <div>
                                <!-- Select All -->
                                <div class="flex items-center justify-between p-3 bg-slate-800/50 rounded-lg mb-2">
                                    <label class="flex items-center gap-2 cursor-pointer">
                                        <input type="checkbox" @change="toggleSelectAll($event)" x-ref="selectAll"
                                            class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                        <span class="text-sm font-medium text-white">Pilih Semua</span>
                                    </label>
                                    <span class="text-sm text-slate-400">
                                        <span x-text="selectedCount"></span> dari <span x-text="usersList.length"></span>
                                        siswa dipilih
                                    </span>
                                </div>

                                <!-- Students List -->
                                <div class="max-h-[350px] overflow-y-auto space-y-1 custom-scrollbar">
                                    <template x-for="user in usersList" :key="user.id">
                                        <label
                                            class="flex items-center gap-3 p-3 rounded-lg hover:bg-slate-800/30 cursor-pointer transition-colors">
                                            <input type="checkbox" name="user_ids[]" :value="user.id"
                                                @change="updateSelectedCount()"
                                                class="user-checkbox w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                            <div class="flex items-center gap-3 flex-1">
                                                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-600 to-blue-600 flex items-center justify-center text-white font-medium text-xs"
                                                    x-text="user.name.charAt(0).toUpperCase()">
                                                </div>
                                                <div class="flex-1">
                                                    <p class="text-sm font-medium text-white" x-text="user.name"></p>
                                                    <p class="text-xs text-slate-400">
                                                        <span x-text="user.username"></span> • <span
                                                            x-text="user.kelas"></span>
                                                    </p>
                                                </div>
                                                <span class="px-2 py-1 rounded-lg text-xs font-medium"
                                                    :class="user.is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20'"
                                                    x-text="user.is_active ? 'Aktif' : 'Nonaktif'">
                                                </span>
                                            </div>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </template>

                        <!-- Empty State -->
                        <template x-if="selectedKelas && !loadingUsers && usersList.length === 0">
                            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                                </svg>
                                <p>Tidak ada siswa di kelas ini</p>
                            </div>
                        </template>

                        <!-- Prompt to select kelas -->
                        <template x-if="!selectedKelas && !loadingUsers">
                            <div class="flex flex-col items-center justify-center py-8 text-slate-400">
                                <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                                </svg>
                                <p>Silakan pilih kelas terlebih dahulu</p>
                            </div>
                        </template>

                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                            <button type="submit" :disabled="selectedCount === 0"
                                class="flex-1 px-6 py-2.5 bg-gradient-to-r from-amber-500 to-orange-500 text-white font-medium rounded-xl hover:from-amber-600 hover:to-orange-600 transition-all shadow-lg shadow-amber-500/20 disabled:opacity-50 disabled:cursor-not-allowed">
                                Update Status
                            </button>
                            <button type="button" @click="showStatusModal = false"
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
        <div x-data x-init="showModal = true"></div>
    @endif
@endsection

@push('scripts')
    <script>
        function statusModal() {
            return {
                showModal: false,
                showStatusModal: false,
                selectedCount: 0,
                selectedKelas: '',
                usersList: [],
                loadingUsers: false,

                fetchUsersByKelas() {
                    if (!this.selectedKelas) {
                        this.usersList = [];
                        this.selectedCount = 0;
                        return;
                    }

                    this.loadingUsers = true;
                    this.usersList = [];
                    this.selectedCount = 0;

                    fetch(`{{ url('admin/user-siswa/by-kelas') }}/${this.selectedKelas}`)
                        .then(response => response.json())
                        .then(data => {
                            this.usersList = data;
                            this.loadingUsers = false;
                        })
                        .catch(error => {
                            console.error('Error:', error);
                            this.loadingUsers = false;
                        });
                },

                toggleSelectAll(event) {
                    this.$nextTick(() => {
                        const checkboxes = document.querySelectorAll('.user-checkbox');
                        checkboxes.forEach(cb => cb.checked = event.target.checked);
                        this.updateSelectedCount();
                    });
                },

                updateSelectedCount() {
                    this.$nextTick(() => {
                        const checkboxes = document.querySelectorAll('.user-checkbox:checked');
                        this.selectedCount = checkboxes.length;

                        // Update select all checkbox state
                        const allCheckboxes = document.querySelectorAll('.user-checkbox');
                        if (this.$refs.selectAll) {
                            this.$refs.selectAll.checked = checkboxes.length === allCheckboxes.length && allCheckboxes.length > 0;
                            this.$refs.selectAll.indeterminate = checkboxes.length > 0 && checkboxes.length < allCheckboxes.length;
                        }
                    });
                }
            }
        }

        // Toggle status function for individual user
        function toggleStatus(userId, button) {
            button.disabled = true;
            button.style.opacity = '0.5';

            fetch(`{{ url('admin/user-siswa') }}/${userId}/toggle-status`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const isActive = data.is_active;
                        const statusText = button.querySelector('.status-text');
                        const statusIcon = button.querySelector('.status-icon');

                        // Update text
                        statusText.textContent = isActive ? 'Aktif' : 'Nonaktif';

                        // Update classes
                        button.className = button.className
                            .replace(/bg-emerald-500\/10|bg-slate-500\/10/g, isActive ? 'bg-emerald-500/10' : 'bg-slate-500/10')
                            .replace(/text-emerald-400|text-slate-400/g, isActive ? 'text-emerald-400' : 'text-slate-400')
                            .replace(/border-emerald-500\/20|border-slate-500\/20/g, isActive ? 'border-emerald-500/20' : 'border-slate-500/20')
                            .replace(/hover:bg-emerald-500\/20|hover:bg-slate-500\/20/g, isActive ? 'hover:bg-emerald-500/20' : 'hover:bg-slate-500/20');

                        // Update icon
                        if (isActive) {
                            statusIcon.innerHTML = '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                        } else {
                            statusIcon.innerHTML = '<svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>';
                        }

                        button.dataset.active = isActive ? '1' : '0';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Gagal mengubah status');
                })
                .finally(() => {
                    button.disabled = false;
                    button.style.opacity = '1';
                });
        }

        // Reset password function
        function resetPassword(userId, userName, button) {
            if (!confirm(`Reset password ${userName} ke NISN?`)) {
                return;
            }
            
            button.disabled = true;
            button.style.opacity = '0.5';
            
            fetch(`{{ url('admin/user-siswa') }}/${userId}/reset-password`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(data.message);
                } else {
                    alert(data.message || 'Gagal reset password');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Gagal reset password');
            })
            .finally(() => {
                button.disabled = false;
                button.style.opacity = '1';
            });
        }
    </script>
@endpush