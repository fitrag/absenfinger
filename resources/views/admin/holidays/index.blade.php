@extends('layouts.admin')

@section('title', 'Pengaturan Hari Libur')
@section('page-title', 'Pengaturan Hari Libur')

@section('content')
    <div class="space-y-6">
        <!-- Header -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h2 class="text-xl font-bold text-white">Pengaturan Hari Libur Nasional</h2>
                <p class="text-sm text-slate-400 mt-1">Kelola hari libur nasional yang tidak dihitung sebagai hari kehadiran</p>
            </div>
            <div class="flex gap-2">
                <button onclick="openAddModal()"
                    class="inline-flex items-center gap-2 px-4 py-2 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all shadow-lg shadow-blue-500/25 hover:shadow-blue-500/40 cursor-pointer">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                    </svg>
                    Tambah Hari Libur
                </button>
            </div>
        </div>

        <!-- Year Filter -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 p-4">
            <form action="{{ route('admin.holidays.index') }}" method="GET" class="flex flex-wrap items-center gap-4">
                <div>
                    <label class="block text-xs text-slate-400 mb-1">Tahun</label>
                    <select name="year" onchange="this.form.submit()"
                        class="px-4 py-2 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                        @foreach($years as $y)
                            <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Table -->
        <div class="rounded-xl bg-slate-900/50 border border-slate-800/50 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-slate-800/50">
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">No</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Tanggal</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Nama Hari Libur</th>
                            <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">Keterangan</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Status</th>
                            <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/50">
                        @forelse($holidays as $index => $holiday)
                            <tr class="hover:bg-slate-800/30 transition-colors">
                                <td class="px-4 py-3 text-slate-400">{{ $holidays->firstItem() + $index }}</td>
                                <td class="px-4 py-3 text-white font-medium">
                                    {{ \Carbon\Carbon::parse($holiday->date)->isoFormat('dddd, D MMMM Y') }}
                                </td>
                                <td class="px-4 py-3 text-slate-300">{{ $holiday->name }}</td>
                                <td class="px-4 py-3 text-slate-400 text-xs">{{ $holiday->description ?? '-' }}</td>
                                <td class="px-4 py-3 text-center">
                                    <form action="{{ route('admin.holidays.toggle', $holiday->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="cursor-pointer">
                                            @if($holiday->is_active)
                                                <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 rounded-lg text-xs font-medium">Aktif</span>
                                            @else
                                                <span class="px-2 py-1 bg-slate-500/20 text-slate-400 rounded-lg text-xs font-medium">Nonaktif</span>
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td class="px-4 py-3 text-center">
                                    <div class="flex items-center justify-center gap-1">
                                        <button onclick='openEditModal(@json($holiday))'
                                            class="p-1.5 text-blue-400 hover:bg-blue-500/20 rounded-lg transition-colors cursor-pointer">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                            </svg>
                                        </button>
                                        <form action="{{ route('admin.holidays.destroy', $holiday->id) }}" method="POST" class="inline" onsubmit="return confirm('Yakin ingin menghapus hari libur ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 text-red-400 hover:bg-red-500/20 rounded-lg transition-colors cursor-pointer">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-4 py-12 text-center text-slate-400">
                                    <svg class="w-12 h-12 mx-auto mb-3 text-slate-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                    </svg>
                                    Belum ada data hari libur untuk tahun {{ $year }}
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($holidays->hasPages())
                <div class="px-4 py-3 border-t border-slate-800/50">
                    {{ $holidays->withQueryString()->links() }}
                </div>
            @endif
        </div>
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-md m-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Tambah Hari Libur</h3>
                <button onclick="closeAddModal()" class="text-slate-400 hover:text-white cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form action="{{ route('admin.holidays.store') }}" method="POST">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal <span class="text-red-400">*</span></label>
                        <input type="date" name="date" required
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Hari Libur <span class="text-red-400">*</span></label>
                        <input type="text" name="name" required placeholder="Contoh: Hari Raya Idul Fitri"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                        <textarea name="description" rows="2" placeholder="Keterangan tambahan (opsional)"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 resize-none"></textarea>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="closeAddModal()"
                            class="flex-1 px-4 py-2.5 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 hidden items-center justify-center">
        <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-md m-4">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-white">Edit Hari Libur</h3>
                <button onclick="closeEditModal()" class="text-slate-400 hover:text-white cursor-pointer">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
            <form id="editForm" method="POST">
                @csrf
                @method('PUT')
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Tanggal <span class="text-red-400">*</span></label>
                        <input type="date" name="date" id="editDate" required
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Nama Hari Libur <span class="text-red-400">*</span></label>
                        <input type="text" name="name" id="editName" required
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-300 mb-2">Keterangan</label>
                        <textarea name="description" id="editDescription" rows="2"
                            class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50 focus:ring-1 focus:ring-blue-500/50 resize-none"></textarea>
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="editIsActive" value="1"
                            class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-blue-500 focus:ring-blue-500/50">
                        <label for="editIsActive" class="text-sm text-slate-300">Aktif</label>
                    </div>
                    <div class="flex gap-3 pt-4">
                        <button type="button" onclick="closeEditModal()"
                            class="flex-1 px-4 py-2.5 bg-slate-700 text-slate-200 font-medium rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">
                            Batal
                        </button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-gradient-to-r from-blue-500 to-indigo-600 text-white font-medium rounded-xl hover:from-blue-600 hover:to-indigo-700 transition-all cursor-pointer">
                            Simpan
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    @if(session('success'))
        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl shadow-lg flex items-center gap-2 text-sm z-50">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
            </svg>
            {{ session('success') }}
        </div>
    @endif

    <script>
        function openAddModal() {
            document.getElementById('addModal').classList.remove('hidden');
            document.getElementById('addModal').classList.add('flex');
        }

        function closeAddModal() {
            document.getElementById('addModal').classList.add('hidden');
            document.getElementById('addModal').classList.remove('flex');
        }

        function openEditModal(holiday) {
            document.getElementById('editForm').action = "{{ url('admin/holidays') }}/" + holiday.id;
            document.getElementById('editDate').value = holiday.date.split('T')[0];
            document.getElementById('editName').value = holiday.name;
            document.getElementById('editDescription').value = holiday.description || '';
            document.getElementById('editIsActive').checked = holiday.is_active;
            document.getElementById('editModal').classList.remove('hidden');
            document.getElementById('editModal').classList.add('flex');
        }

        function closeEditModal() {
            document.getElementById('editModal').classList.add('hidden');
            document.getElementById('editModal').classList.remove('flex');
        }

        // Close modals on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeAddModal();
                closeEditModal();
            }
        });

        // Close modals on backdrop click
        document.getElementById('addModal').addEventListener('click', function(e) {
            if (e.target === this) closeAddModal();
        });
        document.getElementById('editModal').addEventListener('click', function(e) {
            if (e.target === this) closeEditModal();
        });
    </script>
@endsection
