@props(['headers', 'data', 'fields', 'type' => null, 'guruId' => null, 'formFields' => []])

<div class="overflow-x-auto" x-data="{ showAddModal: false, showEditModal: false, editData: {} }">
    @if($type && $guruId)
        <div class="p-4 border-b border-slate-800/50 flex justify-end">
            <button @click="showAddModal = true; editData = {}"
                class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-blue-500/20 text-blue-400 rounded-lg text-sm font-medium hover:bg-blue-500/30 transition-colors cursor-pointer">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                </svg>
                Tambah
            </button>
        </div>
    @endif

    @if($data->count() > 0)
        <table class="w-full">
            <thead>
                <tr class="border-b border-slate-800/50">
                    @foreach($headers as $header)
                        <th class="px-4 py-3 text-left text-xs font-semibold text-slate-400 uppercase tracking-wider">
                            {{ $header }}
                        </th>
                    @endforeach
                    @if($type && $guruId)
                        <th class="px-4 py-3 text-center text-xs font-semibold text-slate-400 uppercase tracking-wider w-24">
                            Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-800/50">
                @foreach($data as $row)
                    <tr class="hover:bg-slate-800/30 transition-colors">
                        @foreach($fields as $field)
                            <td class="px-4 py-3 text-sm text-slate-300">
                                @php
                                    $value = $row->{$field};
                                    if ($value instanceof \Carbon\Carbon) {
                                        $value = $value->format('d/m/Y');
                                    } elseif (is_bool($value)) {
                                        $value = $value ? 'Ya' : 'Tidak';
                                    } elseif (is_numeric($value) && ($field === 'nominal' || $field === 'gapok')) {
                                        $value = 'Rp ' . number_format($value, 0, ',', '.');
                                    }
                                @endphp
                                {{ $value ?? '-' }}
                            </td>
                        @endforeach
                        @if($type && $guruId)
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1">
                                    <button @click="editData = {{ $row->toJson() }}; showEditModal = true"
                                        class="p-1.5 rounded-lg bg-amber-500/20 text-amber-400 hover:bg-amber-500/30 transition-colors cursor-pointer"
                                        title="Edit">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                        </svg>
                                    </button>
                                    <form action="{{ route('admin.guru.riwayat.destroy', [$guruId, $type, $row->id]) }}"
                                        method="POST" class="inline" onsubmit="return confirm('Yakin hapus data ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            class="p-1.5 rounded-lg bg-rose-500/20 text-rose-400 hover:bg-rose-500/30 transition-colors cursor-pointer"
                                            title="Hapus">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        @endif
                    </tr>
                @endforeach
            </tbody>
        </table>
    @else
        <div class="p-8 text-center">
            <svg class="w-12 h-12 mx-auto text-slate-600 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
            </svg>
            <p class="text-slate-400">Belum ada data</p>
        </div>
    @endif

    @if($type && $guruId && count($formFields) > 0)
        <!-- Add Modal -->
        <div x-show="showAddModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-20 overflow-y-auto"
            @keydown.escape.window="showAddModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4"
                @click.outside="showAddModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white">Tambah Data</h3>
                    <button @click="showAddModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form action="{{ route('admin.guru.riwayat.store', [$guruId, $type]) }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        @foreach($formFields as $field)
                            <div class="{{ $field['class'] ?? '' }}">
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">{{ $field['label'] }}</label>
                                @if(($field['type'] ?? 'text') === 'select')
                                    <select name="{{ $field['name'] }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                        <option value="">Pilih</option>
                                        @foreach($field['options'] ?? [] as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($field['type'] ?? 'text') === 'textarea')
                                    <textarea name="{{ $field['name'] }}" rows="2"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"></textarea>
                                @else
                                    <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showAddModal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-700 text-white rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">Simpan</button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Edit Modal -->
        <div x-show="showEditModal" x-cloak
            class="fixed inset-0 bg-black/50 backdrop-blur-sm z-50 flex items-start justify-center pt-20 overflow-y-auto"
            @keydown.escape.window="showEditModal = false">
            <div class="bg-slate-900 rounded-2xl border border-slate-800 p-6 w-full max-w-2xl m-4"
                @click.outside="showEditModal = false">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-bold text-white">Edit Data</h3>
                    <button @click="showEditModal = false" class="text-slate-400 hover:text-white cursor-pointer">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
                <form :action="`{{ url('admin/guru/' . $guruId . '/riwayat/' . $type) }}/${editData.id}`" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        @foreach($formFields as $field)
                            <div class="{{ $field['class'] ?? '' }}">
                                <label class="block text-sm font-medium text-slate-300 mb-1.5">{{ $field['label'] }}</label>
                                @if(($field['type'] ?? 'text') === 'select')
                                    <select name="{{ $field['name'] }}" x-model="editData.{{ $field['name'] }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                        <option value="">Pilih</option>
                                        @foreach($field['options'] ?? [] as $opt)
                                            <option value="{{ $opt }}">{{ $opt }}</option>
                                        @endforeach
                                    </select>
                                @elseif(($field['type'] ?? 'text') === 'textarea')
                                    <textarea name="{{ $field['name'] }}" x-model="editData.{{ $field['name'] }}" rows="2"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50"></textarea>
                                @else
                                    <input type="{{ $field['type'] ?? 'text' }}" name="{{ $field['name'] }}"
                                        x-model="editData.{{ $field['name'] }}"
                                        class="w-full px-4 py-2.5 bg-slate-800/50 border border-slate-700/50 rounded-xl text-white text-sm focus:outline-none focus:border-blue-500/50">
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="flex gap-3">
                        <button type="button" @click="showEditModal = false"
                            class="flex-1 px-4 py-2.5 bg-slate-700 text-white rounded-xl hover:bg-slate-600 transition-colors cursor-pointer">Batal</button>
                        <button type="submit"
                            class="flex-1 px-4 py-2.5 bg-blue-500 text-white rounded-xl hover:bg-blue-600 transition-colors cursor-pointer">Update</button>
                    </div>
                </form>
            </div>
        </div>
    @endif
</div>