@extends('layouts.admin')
@section('title', 'Ujian US')
@section('content')
    <div class="space-y-6" x-data="ujianUsPage()">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div>
                <h1 class="text-2xl font-bold text-white">Ujian US</h1>
                <p class="text-slate-400 text-sm mt-1">Kelola jadwal Ujian Sekolah (US)</p>
            </div>
            <button @click="showAddModal = true"
                class="inline-flex items-center gap-2 px-4 py-2.5 bg-gradient-to-r from-purple-500 to-pink-500 text-white font-medium rounded-xl cursor-pointer"><svg
                    class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg><span>Tambah Ujian</span></button>
        </div>
        <div class="rounded-xl bg-slate-800/50 border border-slate-700/50 overflow-hidden">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-700/50">
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">No</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Nama Ujian</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Mapel</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Tingkat</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Tanggal</th>
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400">Status</th>
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-700/50">@forelse($ujians as $i => $u)<tr class="hover:bg-slate-800/30">
                    <td class="px-4 py-3 text-sm text-slate-400">{{ $ujians->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-sm text-white">{{ $u->nama_ujian }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->mapel->nm_mapel ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->tingkat ?? '-' }}</td>
                    <td class="px-4 py-3 text-sm text-slate-300">{{ $u->tanggal->format('d M Y') }}</td>
                    <td class="px-4 py-3"><span
                            class="px-2 py-1 text-xs rounded-lg {{ $u->status == 'aktif' ? 'bg-emerald-500/20 text-emerald-300' : 'bg-slate-500/20 text-slate-300' }}">{{ ucfirst($u->status) }}</span>
                    </td>
                    <td class="px-4 py-3 text-center"><button @click="openEditModal({{ json_encode($u) }})"
                            class="p-1.5 bg-blue-500/20 text-blue-400 rounded-lg"><svg class="w-4 h-4" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                            </svg></button>
                        <form action="{{ route('admin.soal.ujian-us.destroy', $u->id) }}" method="POST" class="inline"
                            onsubmit="return confirm('Hapus?')">@csrf @method('DELETE')<button
                                class="p-1.5 bg-red-500/20 text-red-400 rounded-lg"><svg class="w-4 h-4" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg></button></form>
                    </td>
                </tr>@empty<tr>
                        <td colspan="7" class="px-4 py-12 text-center text-slate-400">Belum ada ujian US</td>
                    </tr>@endforelse</tbody>
            </table>
            @if($ujians->hasPages())
            <div class="px-4 py-3 border-t border-slate-700/50">{{ $ujians->links() }}</div>@endif
        </div>
        <div x-show="showAddModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="showAddModal = false" style="display:none">
            <div class="w-full max-w-2xl bg-slate-900 border border-slate-700/50 rounded-2xl">
                <div class="flex justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Tambah Ujian US</h3><button @click="showAddModal = false"
                        class="text-slate-400">&times;</button>
                </div>
                <form action="{{ route('admin.soal.ujian-us.store') }}" method="POST" class="p-6 space-y-4">@csrf
                    <input type="text" name="nama_ujian" placeholder="Nama Ujian" required
                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                    <div class="grid grid-cols-2 gap-4"><select name="mapel_id" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                            <option value="">Mapel</option>@foreach($mapels as $m)<option value="{{ $m->id }}">
                                {{ $m->nm_mapel }}
                            </option>@endforeach
                        </select><select name="tingkat" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                            <option value="">Tingkat</option>
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select></div>
                    <div class="grid grid-cols-2 gap-4"><select name="semester" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                            <option value="Ganjil">Ganjil</option>
                            <option value="Genap">Genap</option>
                        </select><select name="tp_id" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">@foreach($tahunPelajarans as $tp)
                                <option value="{{ $tp->id }}" {{ ($activeTp && $activeTp->id == $tp->id) ? 'selected' : '' }}>
                                    {{ $tp->nm_tp }}
                            </option>@endforeach
                        </select></div>
                    <div class="grid grid-cols-3 gap-4"><input type="date" name="tanggal" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"><input type="time"
                            name="waktu_mulai" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"><input type="time"
                            name="waktu_selesai" required
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"></div>
                    <input type="number" name="durasi" placeholder="Durasi (menit)" required
                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                    <div class="flex justify-end gap-3"><button type="button" @click="showAddModal = false"
                            class="px-4 py-2 text-slate-400">Batal</button><button type="submit"
                            class="px-6 py-2 bg-purple-500 text-white rounded-xl">Simpan</button></div>
                </form>
            </div>
        </div>
        <div x-show="showEditModal" x-transition class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50"
            @click.self="showEditModal = false" style="display:none">
            <div class="w-full max-w-2xl bg-slate-900 border border-slate-700/50 rounded-2xl">
                <div class="flex justify-between px-6 py-4 border-b border-slate-700/50">
                    <h3 class="text-lg font-bold text-white">Edit Ujian US</h3><button @click="showEditModal = false"
                        class="text-slate-400">&times;</button>
                </div>
                <form :action="`{{ url('admin/soal/ujian-us') }}/${editUjian.id}`" method="POST" class="p-6 space-y-4">@csrf
                    @method('PUT')
                    <input type="text" name="nama_ujian" x-model="editUjian.nama_ujian"
                        class="w-full px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                    <div class="grid grid-cols-2 gap-4"><select name="mapel_id" x-model="editUjian.mapel_id"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">@foreach($mapels as $m)
                            <option value="{{ $m->id }}">{{ $m->nm_mapel }}</option>@endforeach
                        </select><select name="tingkat" x-model="editUjian.tingkat"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                            <option value="X">X</option>
                            <option value="XI">XI</option>
                            <option value="XII">XII</option>
                        </select></div>
                    <div class="grid grid-cols-3 gap-4"><input type="date" name="tanggal" x-model="editUjian.tanggal"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"><input type="time"
                            name="waktu_mulai" x-model="editUjian.waktu_mulai"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"><input type="time"
                            name="waktu_selesai" x-model="editUjian.waktu_selesai"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"></div>
                    <div class="grid grid-cols-2 gap-4"><input type="number" name="durasi" x-model="editUjian.durasi"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white"><select
                            name="status" x-model="editUjian.status"
                            class="px-4 py-2 bg-slate-800 border border-slate-700 rounded-xl text-white">
                            <option value="draft">Draft</option>
                            <option value="aktif">Aktif</option>
                            <option value="selesai">Selesai</option>
                        </select></div>
                    <div class="flex justify-end gap-3"><button type="button" @click="showEditModal = false"
                            class="px-4 py-2 text-slate-400">Batal</button><button type="submit"
                            class="px-6 py-2 bg-purple-500 text-white rounded-xl">Simpan</button></div>
                </form>
            </div>
        </div>
    </div>
    <script>function ujianUsPage() { return { showAddModal: false, showEditModal: false, editUjian: {}, init() { this.$watch('showAddModal', v => document.body.classList.toggle('overflow-hidden', v)); this.$watch('showEditModal', v => document.body.classList.toggle('overflow-hidden', v)) }, openEditModal(u) { this.editUjian = { ...u, tanggal: u.tanggal ? u.tanggal.split('T')[0] : '' }; this.showEditModal = true } } }</script>
    @if(session('success'))
        <div x-data="{show:true}" x-show="show" x-init="setTimeout(()=>show=false,5000)"
            class="fixed bottom-4 right-4 px-4 py-3 bg-emerald-500 text-white rounded-xl z-50">{{ session('success') }}</div>
    @endif
@endsection