@extends('layouts.admin')

@section('title', 'Aktivitas ' . $user->name)

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.sessions.index') }}"
                    class="p-2 bg-slate-800 hover:bg-slate-700 rounded-lg transition-colors">
                    <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h1 class="text-2xl font-bold text-white">Aktivitas {{ $user->name }}</h1>
                    <p class="text-slate-400 text-sm mt-1">Riwayat aktivitas pengguna di sistem</p>
                </div>
            </div>
            <!-- Date Filter -->
            <form method="GET" action="{{ route('admin.sessions.activities', $user->id) }}" class="flex items-center gap-2">
                <input type="date" name="date" value="{{ $date }}"
                    class="px-3 py-2 bg-slate-800/50 border border-slate-700/50 rounded-lg text-white text-sm"
                    onchange="this.form.submit()">
            </form>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- User Info Card -->
            <div class="lg:col-span-1 space-y-4">
                <!-- Profile Card -->
                <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-6">
                    <div class="flex items-center gap-4 mb-4">
                        @if($user->foto)
                            <img src="{{ asset('storage/' . $user->foto) }}" class="w-16 h-16 rounded-xl object-cover" alt="">
                        @else
                            <div
                                class="w-16 h-16 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-xl">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <p class="text-lg font-bold text-white">{{ $user->name }}</p>
                            <p class="text-sm text-slate-400">{{ $user->username }}</p>
                            <span class="inline-block mt-1 px-2 py-0.5 rounded text-xs font-medium
                                    {{ $user->level === 'admin' ? 'bg-purple-500/20 text-purple-400' :
        ($user->level === 'guru' ? 'bg-blue-500/20 text-blue-400' :
            'bg-slate-500/20 text-slate-400') }}">
                                {{ ucfirst($user->level) }}
                            </span>
                        </div>
                    </div>

                    <div class="pt-4 border-t border-slate-700/50">
                        <p class="text-xs text-slate-500 uppercase font-semibold mb-2">Status</p>
                        @php
                            $latestSession = $sessions->first();
                            $isOnline = $latestSession &&
                                $latestSession->is_online &&
                                $latestSession->last_activity &&
                                $latestSession->last_activity->diffInMinutes(now()) < 5;
                        @endphp
                        @if($isOnline)
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                                Online Sekarang
                            </span>
                        @else
                            <span
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                                Offline
                            </span>
                        @endif
                    </div>
                </div>

                <!-- Recent Sessions -->
                <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 p-6">
                    <h3 class="text-sm font-semibold text-white mb-4">Riwayat Sesi</h3>
                    <div class="space-y-3">
                        @forelse($sessions as $session)
                            <div class="p-3 rounded-lg bg-slate-900/50 border border-slate-700/30">
                                <div class="flex items-start justify-between">
                                    <div>
                                        <p class="text-xs text-slate-300">{{ $session->device_name }}</p>
                                        <p class="text-xs text-slate-500">{{ $session->location }}</p>
                                        <p class="text-xs text-slate-500">{{ $session->ip_address }}</p>
                                    </div>
                                    @if($session->logged_out_at)
                                        <span class="text-xs text-slate-500">Logout</span>
                                    @else
                                        <span class="text-xs text-emerald-400">Aktif</span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-500 mt-2">
                                    Login: {{ $session->logged_in_at ? $session->logged_in_at->format('d M Y H:i') : '-' }}
                                </p>
                            </div>
                        @empty
                            <p class="text-sm text-slate-500">Belum ada sesi</p>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Activity Timeline -->
            <div class="lg:col-span-2">
                <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
                    <div class="px-6 py-4 border-b border-slate-700/50">
                        <h3 class="text-lg font-semibold text-white">
                            Aktivitas Tanggal {{ \Carbon\Carbon::parse($date)->format('d M Y') }}
                        </h3>
                    </div>

                    <div class="p-6">
                        @if($activities->count() > 0)
                            <div class="relative">
                                <!-- Timeline line -->
                                <div class="absolute left-4 top-0 bottom-0 w-0.5 bg-slate-700"></div>

                                <div class="space-y-4">
                                    @foreach($activities as $activity)
                                                        <div class="relative pl-10">
                                                            <!-- Timeline dot -->
                                                            <div
                                                                class="absolute left-2.5 w-3 h-3 rounded-full 
                                                                            {{ $activity->action === 'login' ? 'bg-emerald-500' :
                                        ($activity->action === 'logout' ? 'bg-red-500' :
                                            ($activity->action === 'session_kicked' ? 'bg-amber-500' : 'bg-blue-500')) }}">
                                                            </div>

                                                            <div class="p-3 rounded-lg bg-slate-900/50 border border-slate-700/30">
                                                                <div class="flex items-start justify-between gap-4">
                                                                    <div>
                                                                        <span
                                                                            class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium
                                                                                        {{ $activity->action === 'login' ? 'bg-emerald-500/20 text-emerald-400' :
                                        ($activity->action === 'logout' ? 'bg-red-500/20 text-red-400' :
                                            ($activity->action === 'session_kicked' ? 'bg-amber-500/20 text-amber-400' : 'bg-blue-500/20 text-blue-400')) }}">
                                                                            {{ ucfirst(str_replace('_', ' ', $activity->action)) }}
                                                                        </span>
                                                                        <p class="text-sm text-slate-300 mt-1">{{ $activity->description }}</p>
                                                                        @if($activity->url)
                                                                            <p class="text-xs text-slate-500 mt-1">
                                                                                <span class="text-slate-600">URL:</span> /{{ $activity->url }}
                                                                            </p>
                                                                        @endif
                                                                    </div>
                                                                    <span class="text-xs text-slate-500 whitespace-nowrap">
                                                                        {{ $activity->created_at->format('H:i:s') }}
                                                                    </span>
                                                                </div>
                                                            </div>
                                                        </div>
                                    @endforeach
                                </div>
                            </div>

                            <div class="mt-6">
                                {{ $activities->appends(request()->all())->links() }}
                            </div>
                        @else
                            <div class="text-center py-8">
                                <svg class="w-12 h-12 text-slate-600 mx-auto mb-3" fill="none" stroke="currentColor"
                                    viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                                </svg>
                                <p class="text-slate-400">Tidak ada aktivitas pada tanggal ini</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection