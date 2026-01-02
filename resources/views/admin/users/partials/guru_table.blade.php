<div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-800/50">
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">User
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                        Username</th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Role
                    </th>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Status
                    </th>
                    <th class="px-4 py-3 text-right text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @forelse($users as $index => $user)
                    <tr class="hover:bg-slate-800/30 transition-colors"
                        data-searchable="{{ $user->name }} {{ $user->username }} {{ $user->role?->nama_role ?? '' }}">
                        <td class="px-4 py-3 text-sm text-slate-400">{{ $users->firstItem() + $index }}</td>
                        <td class="px-4 py-3">
                            <div class="flex items-center gap-3">
                                @if($user->foto)
                                    <img src="{{ Storage::url($user->foto) }}" class="w-8 h-8 rounded-full object-cover">
                                @else
                                    <div
                                        class="w-8 h-8 rounded-full bg-gradient-to-br from-cyan-600 to-blue-600 flex items-center justify-center text-white font-medium text-xs">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                @endif
                                <span class="text-sm font-medium text-white">{{ $user->name }}</span>
                            </div>
                        </td>
                        <td class="px-4 py-3 text-sm text-slate-300 font-mono">{{ $user->username }}</td>
                        <td class="px-4 py-3">
                            @if($user->roles->count() > 0)
                                <div class="flex flex-wrap gap-1">
                                    @foreach($user->roles as $role)
                                        <span
                                            class="inline-flex px-2 py-1 rounded-lg text-xs font-medium bg-purple-500/10 text-purple-400 border border-purple-500/20">
                                            {{ $role->nama_role }}
                                        </span>
                                    @endforeach
                                </div>
                            @else
                                <span class="text-slate-500">-</span>
                            @endif
                        </td>
                        <td class="px-4 py-3">
                            <button type="button" onclick="toggleStatusGuru({{ $user->id }}, this)"
                                title="Klik untuk {{ $user->is_active ? 'nonaktifkan' : 'aktifkan' }} user"
                                class="status-toggle inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-medium transition-all duration-200 hover:scale-110 hover:shadow-lg {{ $user->is_active ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 hover:bg-emerald-500/30 hover:border-emerald-500/40 hover:shadow-emerald-500/20' : 'bg-slate-500/10 text-slate-400 border border-slate-500/20 hover:bg-slate-500/30 hover:border-slate-500/40 hover:shadow-slate-500/20' }}"
                                data-active="{{ $user->is_active ? '1' : '0' }}">
                                <span class="status-icon">
                                    @if($user->is_active)
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @else
                                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd"
                                                d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                                clip-rule="evenodd" />
                                        </svg>
                                    @endif
                                </span>
                                <span class="status-text">{{ $user->is_active ? 'Aktif' : 'Nonaktif' }}</span>
                            </button>
                        </td>
                        <td class="px-4 py-3">
                            <div class="flex items-center justify-end gap-2">
                                <!-- Detail Button -->
                                <a href="{{ route('admin.users.show', $user) }}"
                                    class="p-2 rounded-lg bg-blue-500/10 text-blue-400 border border-blue-500/20 hover:bg-blue-500/30 hover:border-blue-500/40 hover:scale-110 transition-all duration-200"
                                    title="Detail">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                </a>
                                <!-- Edit Button -->
                                <a href="{{ route('admin.users.edit', $user) }}"
                                    class="p-2 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20 hover:bg-amber-500/30 hover:border-amber-500/40 hover:scale-110 transition-all duration-200"
                                    title="Edit">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>
                                <!-- Reset Password Button -->
                                <button type="button"
                                    onclick="resetPasswordGuru({{ $user->id }}, '{{ $user->name }}', this)"
                                    class="p-2 rounded-lg bg-cyan-500/10 text-cyan-400 border border-cyan-500/20 hover:bg-cyan-500/30 hover:border-cyan-500/40 hover:scale-110 transition-all duration-200"
                                    title="Reset Password ke Username">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M15 7a2 2 0 012 2m4 0a6 6 0 01-7.743 5.743L11 17H9v2H7v2H4a1 1 0 01-1-1v-2.586a1 1 0 01.293-.707l5.964-5.964A6 6 0 1121 9z" />
                                    </svg>
                                </button>
                                <!-- Delete Button -->
                                <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                    onsubmit="return confirm('Yakin ingin menghapus user ini?')" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="p-2 rounded-lg bg-rose-500/10 text-rose-400 border border-rose-500/20 hover:bg-rose-500/30 hover:border-rose-500/40 hover:scale-110 transition-all duration-200"
                                        title="Hapus">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                        </svg>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-4 py-12 text-center">
                            <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <p class="text-slate-400">Belum ada data user guru</p>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($users->hasPages())
        <div class="px-4 py-3 border-t border-slate-800/50">
            {{ $users->links() }}
        </div>
    @endif
</div>