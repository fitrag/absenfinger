<!-- Sidebar -->
<aside 
    :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed top-0 left-0 z-50 w-72 h-full bg-slate-900/95 backdrop-blur-xl border-r border-slate-800/50 transition-transform duration-300 ease-in-out lg:translate-x-0"
>
    <div class="flex flex-col h-full">
        <!-- Logo Section -->
        <div class="flex items-center justify-between h-16 px-6 border-b border-slate-800/50">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center shadow-lg shadow-purple-500/30">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 11c0 3.517-1.009 6.799-2.753 9.571m-3.44-2.04l.054-.09A13.916 13.916 0 008 11a4 4 0 118 0c0 1.017-.07 2.019-.203 3m-2.118 6.844A21.88 21.88 0 0015.171 17m3.839 1.132c.645-2.266.99-4.659.99-7.132A8 8 0 008 4.07M3 15.364c.64-1.319 1-2.8 1-4.364 0-1.457.39-2.823 1.07-4"/>
                    </svg>
                </div>
                <div>
                    <h1 class="text-lg font-bold text-white">AbsenFinger</h1>
                    <p class="text-xs text-slate-400 -mt-0.5">Admin Panel</p>
                </div>
            </a>
            <!-- Close button for mobile -->
            <button 
                @click="sidebarOpen = false"
                class="lg:hidden p-1.5 rounded-lg text-slate-400 hover:text-white hover:bg-slate-800/50 transition-colors"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 overflow-y-auto p-4 space-y-1">
            <!-- Dashboard -->
            <a href="{{ url('/admin/dashboard') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->is('admin/dashboard') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/dashboard') ? 'text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                <span>Dashboard</span>
                @if(request()->is('admin/dashboard'))
                    <span class="ml-auto w-1.5 h-1.5 rounded-full bg-gradient-to-r from-blue-400 to-purple-400"></span>
                @endif
            </a>

            <!-- Section: Data Master -->
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Data Master</p>
            </div>

            <!-- Students -->
            <a href="{{ url('/admin/students') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->is('admin/students*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/students*') ? 'text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                </svg>
                <span>Siswa</span>
            </a>

            <!-- Section: Attendance -->
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Attendance</p>
            </div>

            <!-- Attendance Records -->
            <a href="{{ url('/admin/attendance') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->is('admin/attendance*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/attendance*') ? 'text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
                </svg>
                <span>Attendance</span>
            </a>

            <!-- Reports -->
            <a href="{{ url('/admin/reports') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->is('admin/reports*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/reports*') ? 'text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                </svg>
                <span>Reports</span>
            </a>

            <!-- Section: Settings -->
            <div class="pt-4 pb-2">
                <p class="px-4 text-xs font-semibold text-slate-500 uppercase tracking-wider">Settings</p>
            </div>

            <!-- Settings -->
            <a href="{{ url('/admin/settings') }}" 
               class="flex items-center gap-3 px-4 py-3 rounded-xl text-sm font-medium transition-all duration-200
                      {{ request()->is('admin/settings*') ? 'bg-gradient-to-r from-blue-500/20 to-purple-500/20 text-white border border-blue-500/30' : 'text-slate-400 hover:text-white hover:bg-slate-800/50' }}">
                <svg class="w-5 h-5 {{ request()->is('admin/settings*') ? 'text-blue-400' : '' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
                <span>Settings</span>
            </a>
        </nav>

        <!-- User Section at Bottom -->
        <div class="p-4 border-t border-slate-800/50">
            <div class="flex items-center gap-3 p-3 rounded-xl bg-slate-800/30">
                <div class="w-10 h-10 rounded-lg bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow-lg shadow-purple-500/20">
                    AU
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-medium text-white truncate">Admin User</p>
                    <p class="text-xs text-slate-400 truncate">admin@absenfinger.com</p>
                </div>
                <a href="{{ url('/logout') }}" class="p-2 rounded-lg text-slate-400 hover:text-red-400 hover:bg-slate-700/50 transition-colors" title="Logout">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</aside>
