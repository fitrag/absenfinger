@extends('layouts.admin')

@section('title', 'Detail Siswa')
@section('page-title', 'Detail Siswa')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Student Info Card -->
    <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
        <div class="p-6 border-b border-slate-800/50 flex items-center justify-between">
            <h3 class="text-lg font-semibold text-white">Informasi Siswa</h3>
            <div class="flex items-center gap-2">
                <a href="{{ route('admin.students.edit', $student) }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-amber-500/10 text-amber-400 border border-amber-500/20 rounded-lg text-sm font-medium hover:bg-amber-500/20 transition-colors">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
                    </svg>
                    Edit
                </a>
                <a href="{{ route('admin.students.index') }}" 
                   class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-slate-700 text-white rounded-lg text-sm font-medium hover:bg-slate-600 transition-colors">
                    ← Kembali
                </a>
            </div>
        </div>
        
        <div class="p-6">
            <div class="flex items-start gap-6">
                <!-- Avatar -->
                <div class="w-20 h-20 rounded-2xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-2xl shadow-lg shadow-purple-500/30">
                    {{ strtoupper(substr($student->name, 0, 2)) }}
                </div>
                
                <!-- Info -->
                <div class="flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Nama Lengkap</p>
                        <p class="text-white font-medium">{{ $student->name }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">NIS</p>
                        <p class="text-white font-mono">{{ $student->nis }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Finger ID</p>
                        <p class="text-white font-mono">{{ $student->finger_id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Status</p>
                        <span class="inline-flex px-2 py-0.5 rounded-lg text-xs font-medium {{ $student->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                            {{ $student->is_active ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Kelas</p>
                        <p class="text-white">{{ $student->class }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-slate-400 mb-0.5">Jurusan</p>
                        <p class="text-white">{{ $student->major }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Recent Attendance -->
    <div class="rounded-2xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
        <div class="p-6 border-b border-slate-800/50">
            <h3 class="text-lg font-semibold text-white">Riwayat Kehadiran Terbaru</h3>
        </div>
        
        <div class="divide-y divide-slate-800/50">
            @forelse($student->attendances as $attendance)
            <div class="flex items-center gap-4 p-4">
                <div class="w-10 h-10 rounded-lg {{ $attendance->checktype == 0 ? 'bg-emerald-500/20' : 'bg-amber-500/20' }} flex items-center justify-center">
                    @if($attendance->checktype == 0)
                    <svg class="w-5 h-5 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"/>
                    </svg>
                    @else
                    <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    @endif
                </div>
                <div class="flex-1">
                    <p class="text-sm text-white">{{ $attendance->checktime->format('l, d F Y') }}</p>
                    <p class="text-xs text-slate-400">{{ $attendance->checktime->format('H:i:s') }}</p>
                </div>
                <span class="px-2 py-1 rounded-lg text-xs font-medium {{ $attendance->checktype == 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                    {{ $attendance->checktype == 0 ? 'Masuk' : 'Pulang' }}
                </span>
            </div>
            @empty
            <div class="p-8 text-center">
                <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <p class="text-slate-400">Belum ada riwayat kehadiran</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
