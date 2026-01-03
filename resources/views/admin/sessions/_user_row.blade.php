@php
    $session = $user->latestSession;
    $isOnline = $session &&
        $session->is_online &&
        $session->last_activity &&
        $session->last_activity->diffInMinutes(now()) < 5;
@endphp
<tr class="hover:bg-slate-800/30 transition-colors">
    <td class="px-4 py-2 text-sm text-slate-400">{{ $loop->iteration }}</td>
    <td class="px-4 py-2">
        <div class="flex items-center gap-3">
            <div class="relative">
                @if($user->foto)
                    <img src="{{ asset('storage/' . $user->foto) }}" class="w-10 h-10 rounded-lg object-cover" alt="">
                @else
                    <div
                        class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-bold text-sm">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                @endif
                <!-- Online indicator dot -->
                <span class="absolute -bottom-0.5 -right-0.5 w-3 h-3 rounded-full border-2 border-slate-800 
                    {{ $isOnline ? 'bg-emerald-500' : 'bg-slate-500' }}"></span>
            </div>
            <div>
                <p class="text-sm font-medium text-white">{{ $user->name }}</p>
                <p class="text-xs text-slate-400">{{ $user->username }}</p>
            </div>
        </div>
    </td>
    <td class="px-4 py-2">
        <span class="px-2.5 py-1 rounded-lg text-xs font-medium
            {{ $user->level === 'admin' ? 'bg-purple-500/20 text-purple-400 border border-purple-500/30' :
    ($user->level === 'guru' ? 'bg-blue-500/20 text-blue-400 border border-blue-500/30' :
        'bg-slate-500/20 text-slate-400 border border-slate-500/30') }}">
            {{ ucfirst($user->level) }}
        </span>
    </td>
    <td class="px-4 py-2 text-center">
        @if($isOnline)
            <span
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                Online
            </span>
        @else
            <span
                class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-medium bg-slate-500/20 text-slate-400 border border-slate-500/30">
                <span class="w-2 h-2 rounded-full bg-slate-500"></span>
                Offline
            </span>
        @endif
    </td>
    <td class="px-4 py-2 text-sm text-slate-300">
        {{ $session->device_name ?? '-' }}
    </td>
    <td class="px-4 py-2 text-sm text-slate-300">
        {{ $session->location ?? '-' }}
    </td>
    <td class="px-4 py-2 text-sm text-slate-400">
        @if($session && $session->last_activity)
            {{ $session->last_activity->diffForHumans() }}
        @else
            -
        @endif
    </td>
    <td class="px-4 py-2 text-center">
        <div class="flex items-center justify-center gap-2">
            <a href="{{ route('admin.sessions.activities', $user->id) }}"
                class="p-2 bg-blue-500/20 text-blue-400 hover:bg-blue-500/30 rounded-lg transition-colors"
                title="Lihat Aktivitas">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                </svg>
            </a>
            @if($session && !$session->logged_out_at)
                <form action="{{ route('admin.sessions.forceLogout', $session->id) }}" method="POST"
                    onsubmit="return confirm('Logout paksa sesi ini?')">
                    @csrf
                    <button type="submit"
                        class="p-2 bg-red-500/20 text-red-400 hover:bg-red-500/30 rounded-lg transition-colors cursor-pointer"
                        title="Force Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1" />
                        </svg>
                    </button>
                </form>
            @endif
        </div>
    </td>
</tr>