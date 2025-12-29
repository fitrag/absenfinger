@extends('layouts.admin')

@section('title', 'Edit User')
@section('page-title', 'Edit User')

@section('content')
    <div class="max-w-2xl mx-auto space-y-6">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}"
                class="p-2 text-slate-400 hover:text-white hover:bg-slate-800/50 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                </svg>
            </a>
            <div>
                <h2 class="text-xl font-bold text-white">Edit User</h2>
                <p class="text-sm text-slate-400 mt-1">Perbarui informasi user</p>
            </div>
        </div>

        <form action="{{ route('admin.users.update', $user) }}" method="POST" enctype="multipart/form-data"
            class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-6 space-y-5">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label for="name" class="block text-sm font-medium text-slate-300 mb-2">Nama <span
                            class="text-rose-400">*</span></label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" required
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    @error('name')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="username" class="block text-sm font-medium text-slate-300 mb-2">Username <span
                            class="text-rose-400">*</span></label>
                    <input type="text" id="username" name="username" value="{{ old('username', $user->username) }}" required
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    @error('username')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password" class="block text-sm font-medium text-slate-300 mb-2">Password <span
                            class="text-xs text-slate-500">(kosongkan jika tidak diubah)</span></label>
                    <input type="password" id="password" name="password"
                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white placeholder-slate-500 focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    @error('password')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-300 mb-2">Role</label>
                    <div class="grid grid-cols-2 md:grid-cols-3 gap-2">
                        @foreach($roles as $role)
                            <label class="flex items-center gap-2 cursor-pointer p-2 rounded-lg bg-slate-800/30 hover:bg-slate-800/50 transition-colors">
                                <input type="checkbox" name="roles[]" value="{{ $role->id }}" 
                                    {{ in_array($role->id, old('roles', $user->roles->pluck('id')->toArray())) ? 'checked' : '' }}
                                    class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-purple-500 focus:ring-purple-500/50">
                                <span class="text-sm text-slate-300">{{ $role->nama_role }}</span>
                            </label>
                        @endforeach
                    </div>
                    @error('roles')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="foto" class="block text-sm font-medium text-slate-300 mb-2">Foto</label>
                @if($user->foto)
                    <div class="mb-2">
                        <img src="{{ Storage::url($user->foto) }}" class="w-20 h-20 rounded-lg object-cover">
                    </div>
                @endif
                <input type="file" id="foto" name="foto" accept="image/*"
                    class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-blue-500/20 file:text-blue-400 hover:file:bg-blue-500/30">
                @error('foto')<p class="mt-1 text-sm text-rose-400">{{ $message }}</p>@enderror
            </div>

            <div class="pt-2">
                <label class="flex items-center gap-2 cursor-pointer">
                    <input type="checkbox" name="is_active" value="1" {{ $user->is_active ? 'checked' : '' }}
                        class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                    <span class="text-sm text-slate-300">Aktif</span>
                </label>
            </div>

            <div class="flex items-center gap-3 pt-4 border-t border-slate-800/50">
                <button type="submit"
                    class="px-6 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all shadow-lg shadow-blue-500/20">Update
                    User</button>
                <a href="{{ route('admin.users.index') }}"
                    class="px-6 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">Batal</a>
            </div>
        </form>
    </div>
@endsection