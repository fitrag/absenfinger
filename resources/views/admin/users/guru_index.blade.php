@extends('layouts.admin')

@section('title', 'Data User Guru')
@section('page-title', 'Data User Guru')

@section('content')
    <div class="space-y-6" x-data="{ showModal: false }">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Data User Guru</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola data user level guru</p>
            </div>
            <button @click="showModal = true"
                class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah User
            </button>
        </div>

        <!-- Search Box and Show Entries -->
        <div class="flex items-center justify-between">
            <form action="{{ route('admin.users.guru') }}" method="GET" class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-300 whitespace-nowrap">Pencarian</label>
                <div class="relative w-64">
                    <input type="text" id="searchInput" name="search" value="{{ request('search') }}" placeholder="Cari nama, username..."
                        class="w-full px-3 pr-8 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    @if(request('search'))
                        <a href="{{ route('admin.users.guru', ['perPage' => request('perPage')]) }}" class="absolute inset-y-0 right-0 pr-2 flex items-center">
                            <svg class="w-4 h-4 text-slate-400 hover:text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </a>
                    @endif
                </div>
                <input type="hidden" name="perPage" value="{{ request('perPage', 10) }}">
                <button type="submit" class="px-3 py-2 bg-blue-500/20 text-blue-400 rounded-lg hover:bg-blue-500/30 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </button>
            </form>
            <div class="flex items-center gap-3">
                <label class="text-sm font-medium text-slate-300 whitespace-nowrap">Tampilkan</label>
                <select class="px-3 py-2 text-sm bg-slate-900/50 border border-slate-800/50 rounded-lg text-white focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50"
                        onchange="window.location.href='{{ route('admin.users.guru') }}?perPage=' + this.value + '&search={{ request('search') }}'">
                    <option value="10" {{ request('perPage') == 10 ? 'selected' : '' }}>10</option>
                    <option value="30" {{ request('perPage') == 30 ? 'selected' : '' }}>30</option>
                    <option value="100" {{ request('perPage') == 100 ? 'selected' : '' }}>100</option>
                    <option value="all" {{ request('perPage') == 'all' ? 'selected' : '' }}>Semua</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div id="users-table-container">
            @include('admin.users.partials.guru_table')
        </div>

        <!-- Add Generic Modal (Example - Can be customized or removed if using same create logic) -->
        <div x-show="showModal" 
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
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
                        <h3 class="text-lg font-bold text-white">Tambah User Guru</h3>
                        <button @click="showModal = false" class="p-1 text-slate-400 hover:text-white transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.users.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-4 overflow-y-auto">
                        @csrf
                        <!-- Hidden Level Input -->
                        <input type="hidden" name="level" value="guru">
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nama <span class="text-rose-400">*</span></label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                @error('name')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <!-- Username -->
                            <div>
                                <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username <span class="text-rose-400">*</span></label>
                                <input type="text" id="username" name="username" value="{{ old('username') }}" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                @error('username')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                            <!-- Password -->
                            <div>
                                <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password <span class="text-rose-400">*</span></label>
                                <input type="password" id="password" name="password" required
                                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                                @error('password')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                            </div>
                             <!-- Roles -->
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                                <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                                    @foreach($roles as $role)
                                        <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-slate-800/30 hover:bg-slate-800/50 transition-colors">
                                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                                {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}
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
                                <input type="checkbox" name="is_active" value="1" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                                <span class="text-sm text-slate-300">Aktif</span>
                            </label>
                        </div>
                        <!-- Buttons -->
                        <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                            <button type="submit" class="flex-1 px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">Simpan</button>
                            <button type="button" @click="showModal = false" class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">Batal</button>
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
    document.addEventListener('DOMContentLoaded', function() {
        const searchInput = document.getElementById('searchInput');
        const tableContainer = document.getElementById('users-table-container');
        let timeout = null;

        if (searchInput && tableContainer) {
            searchInput.addEventListener('input', function(e) {
                clearTimeout(timeout);
                timeout = setTimeout(() => {
                    const query = e.target.value;
                    const url = new URL(window.location.href);
                    
                    if (query) {
                        url.searchParams.set('search', query);
                    } else {
                        url.searchParams.delete('search');
                    }
                    
                    // Reset to page 1 for new search
                    url.searchParams.delete('page');

                    fetch(url.toString(), {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'text/html'
                        }
                    })
                    .then(response => response.text())
                    .then(html => {
                        tableContainer.innerHTML = html;
                        // Update browser URL without reload
                        window.history.pushState({}, '', url.toString());
                    })
                    .catch(error => console.error('Error:', error));
                }, 500); // Debounce 500ms
            });
        }
    });

    window.addEventListener('popstate', function() {
        window.location.reload(); 
    });

    // Toggle status function for guru
    function toggleStatusGuru(userId, button) {
        button.disabled = true;
        button.style.opacity = '0.5';
        
        fetch(`{{ url('admin/user-guru') }}/${userId}/toggle-status`, {
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
                
                // Update tooltip
                button.title = 'Klik untuk ' + (isActive ? 'nonaktifkan' : 'aktifkan') + ' user';
                
                // Update classes
                button.className = button.className
                    .replace(/bg-emerald-500\/10|bg-slate-500\/10/g, isActive ? 'bg-emerald-500/10' : 'bg-slate-500/10')
                    .replace(/text-emerald-400|text-slate-400/g, isActive ? 'text-emerald-400' : 'text-slate-400')
                    .replace(/border-emerald-500\/20|border-slate-500\/20/g, isActive ? 'border-emerald-500/20' : 'border-slate-500/20')
                    .replace(/hover:bg-emerald-500\/30|hover:bg-slate-500\/30/g, isActive ? 'hover:bg-emerald-500/30' : 'hover:bg-slate-500/30')
                    .replace(/hover:border-emerald-500\/40|hover:border-slate-500\/40/g, isActive ? 'hover:border-emerald-500/40' : 'hover:border-slate-500/40')
                    .replace(/hover:shadow-emerald-500\/20|hover:shadow-slate-500\/20/g, isActive ? 'hover:shadow-emerald-500/20' : 'hover:shadow-slate-500/20');
                
                // Update icon
                if (isActive) {
                    statusIcon.innerHTML = '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                } else {
                    statusIcon.innerHTML = '<svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>';
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

    // Reset password function for guru
    function resetPasswordGuru(userId, userName, button) {
        if (!confirm(`Reset password ${userName} ke Username?`)) {
            return;
        }
        
        button.disabled = true;
        button.style.opacity = '0.5';
        
        fetch(`{{ url('admin/user-guru') }}/${userId}/reset-password`, {
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
