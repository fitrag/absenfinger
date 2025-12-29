<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Sertifikat;
use App\Models\Pkl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

class SertifikatController extends Controller
{
    /**
     * Display Sertifikat list
     */
    public function index(Request $request)
    {
        $search = $request->get('search');

        $query = Sertifikat::with(['pkl.student.kelas'])
            ->orderBy('created_at', 'desc');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nomor_sertifikat', 'like', "%{$search}%")
                    ->orWhereHas('pkl.student', function ($sq) use ($search) {
                        $sq->where('nama', 'like', "%{$search}%")
                            ->orWhere('nis', 'like', "%{$search}%");
                    });
            });
        }

        $sertifikats = $query->paginate(15)->withQueryString();

        // Get PKL list for form
        $pklList = Pkl::with('student')
            ->where('status', 'selesai')
            ->whereDoesntHave('sertifikat')
            ->get();

        $stats = [
            'total' => Sertifikat::count(),
        ];

        return view('admin.sertifikat.index', compact('sertifikats', 'pklList', 'search', 'stats'));
    }

    /**
     * Store new Sertifikat
     */
    public function store(Request $request)
    {
        $request->validate([
            'pkl_id' => 'required|exists:pkls,id|unique:sertifikats,pkl_id',
            'nomor_sertifikat' => 'required|string|max:100|unique:sertifikats,nomor_sertifikat',
            'tanggal_terbit' => 'required|date',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except('file_sertifikat');
        $data['created_by'] = Session::get('user_id');

        if ($request->hasFile('file_sertifikat')) {
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('sertifikat', 'public');
        }

        Sertifikat::create($data);

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat berhasil ditambahkan');
    }

    /**
     * Update Sertifikat
     */
    public function update(Request $request, $id)
    {
        $sertifikat = Sertifikat::findOrFail($id);

        $request->validate([
            'nomor_sertifikat' => 'required|string|max:100|unique:sertifikats,nomor_sertifikat,' . $id,
            'tanggal_terbit' => 'required|date',
            'nilai' => 'nullable|numeric|min:0|max:100',
            'predikat' => 'required|in:Sangat Baik,Baik,Cukup,Kurang',
            'file_sertifikat' => 'nullable|file|mimes:pdf,jpg,jpeg,png|max:2048',
            'keterangan' => 'nullable|string',
        ]);

        $data = $request->except(['file_sertifikat', 'pkl_id']);

        if ($request->hasFile('file_sertifikat')) {
            // Delete old file
            if ($sertifikat->file_sertifikat) {
                Storage::disk('public')->delete($sertifikat->file_sertifikat);
            }
            $data['file_sertifikat'] = $request->file('file_sertifikat')->store('sertifikat', 'public');
        }

        $sertifikat->update($data);

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat berhasil diperbarui');
    }

    /**
     * Delete Sertifikat
     */
    public function destroy($id)
    {
        $sertifikat = Sertifikat::findOrFail($id);

        if ($sertifikat->file_sertifikat) {
            Storage::disk('public')->delete($sertifikat->file_sertifikat);
        }

        $sertifikat->delete();

        return redirect()->route('admin.sertifikat.index')->with('success', 'Sertifikat berhasil dihapus');
    }
}
