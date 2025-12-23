@extends('layouts.admin')

@section('title', 'Edit Siswa')
@section('page-title', 'Edit Siswa')

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
        <div class="p-6 border-b border-slate-800/50">
            <h3 class="text-lg font-semibold text-white">Form Edit Siswa</h3>
            <p class="text-sm text-slate-400 mt-1">Perbarui data siswa</p>
        </div>
        
        <form action="{{ route('admin.students.update', $student) }}" method="POST" class="p-6 space-y-5">
            @csrf
            @method('PUT')
            
            <!-- Finger ID -->
            <div>
                <label for="finger_id" class="block text-sm font-medium text-slate-300 mb-1.5">Finger ID <span class="text-rose-400">*</span></label>
                <input type="text" name="finger_id" id="finger_id" value="{{ old('finger_id', $student->finger_id) }}" required
                       class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500">
                @error('finger_id')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- NIS -->
            <div>
                <label for="nis" class="block text-sm font-medium text-slate-300 mb-1.5">NIS <span class="text-rose-400">*</span></label>
                <input type="text" name="nis" id="nis" value="{{ old('nis', $student->nis) }}" required
                       class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500">
                @error('nis')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-slate-300 mb-1.5">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" name="name" id="name" value="{{ old('name', $student->name) }}" required
                       class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500">
                @error('name')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Class -->
            <div>
                <label for="class" class="block text-sm font-medium text-slate-300 mb-1.5">Kelas <span class="text-rose-400">*</span></label>
                <input type="text" name="class" id="class" value="{{ old('class', $student->class) }}" required list="class-list"
                       class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500">
                <datalist id="class-list">
                    @foreach($classes as $class)
                        <option value="{{ $class }}">
                    @endforeach
                </datalist>
                @error('class')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Major -->
            <div>
                <label for="major" class="block text-sm font-medium text-slate-300 mb-1.5">Jurusan <span class="text-rose-400">*</span></label>
                <input type="text" name="major" id="major" value="{{ old('major', $student->major) }}" required list="major-list"
                       class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 placeholder-slate-500">
                <datalist id="major-list">
                    @foreach($majors as $major)
                        <option value="{{ $major }}">
                    @endforeach
                </datalist>
                @error('major')
                    <p class="text-rose-400 text-xs mt-1">{{ $message }}</p>
                @enderror
            </div>
            
            <!-- Is Active -->
            <div class="flex items-center gap-3">
                <input type="checkbox" name="is_active" id="is_active" value="1" {{ $student->is_active ? 'checked' : '' }}
                       class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50 focus:ring-offset-0">
                <label for="is_active" class="text-sm text-slate-300">Siswa Aktif</label>
            </div>
            
            <!-- Buttons -->
            <div class="flex items-center gap-3 pt-4">
                <button type="submit" 
                        class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-purple-500 text-white font-medium rounded-xl hover:from-blue-600 hover:to-purple-600 transition-all">
                    Perbarui
                </button>
                <a href="{{ route('admin.students.index') }}" 
                   class="px-4 py-2.5 bg-slate-700 text-white font-medium rounded-xl hover:bg-slate-600 transition-colors">
                    Batal
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
