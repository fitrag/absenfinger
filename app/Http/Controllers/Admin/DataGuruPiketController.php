<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruPiket;
use App\Models\Role;
use Illuminate\Http\Request;

class DataGuruPiketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $search = $request->get('search');
        $filterHari = $request->get('hari');

        $query = GuruPiket::with(['guru']);

        // Filter by hari
        if ($filterHari) {
            $query->where('hari', $filterHari);
        }

        // Server-side search
        if ($search) {
            $query->whereHas('guru', function ($guruQuery) use ($search) {
                $guruQuery->where('nama', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%");
            });
        }

        $guruPiketData = $query->orderBy('hari')->orderBy('created_at', 'desc')->get();

        // All gurus for edit dropdown
        $allGurus = Guru::orderBy('nama')->get();

        // Get IDs of gurus who are already assigned to ANY day
        // This enforces "One Guru One Piket Day" policy as requested
        $assignedGuruIds = GuruPiket::pluck('guru_id')->unique()->toArray();

        // Available gurus (not yet assigned) for Add dropdown
        $availableGurus = $allGurus->whereNotIn('id', $assignedGuruIds)->values();

        // Available days
        $days = GuruPiket::getDays();

        // Stats by day
        $stats = [];
        foreach ($days as $day) {
            $stats[$day] = GuruPiket::where('hari', $day)->where('is_active', true)->count();
        }

        return view('admin.guru_piket_data.index', compact('guruPiketData', 'availableGurus', 'allGurus', 'days', 'stats', 'filterHari'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'guru_id' => 'required|array',
            'guru_id.*' => 'exists:m_gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'guru_id.array' => 'Format data guru tidak valid',
            'guru_id.*.exists' => 'Guru tidak valid',
            'hari.required' => 'Hari wajib dipilih',
            'hari.in' => 'Hari tidak valid',
        ]);

        $guruIds = $request->guru_id;
        $hari = $request->hari;
        $isActive = $request->has('is_active');
        $successCount = 0;
        $dupCount = 0;

        foreach ($guruIds as $id) {
            // Check if guru already assigned to this day
            $exists = GuruPiket::where('guru_id', $id)
                ->where('hari', $hari)
                ->exists();

            if ($exists) {
                $dupCount++;
                continue;
            }

            // Create the piket assignment
            GuruPiket::create([
                'guru_id' => $id,
                'hari' => $hari,
                'is_active' => $isActive,
            ]);

            // Add "Piket" role to the guru's user
            $this->addPiketRoleToGuru($id);
            $successCount++;
        }

        if ($successCount > 0) {
            $msg = $successCount . ' data guru piket berhasil ditambahkan!';
            if ($dupCount > 0) {
                $msg .= ' (' . $dupCount . ' data dilewati karena duplikat)';
            }
            return redirect()->route('admin.guru-piket-data.index')->with('success', $msg);
        } else {
            return redirect()->back()
                ->withErrors(['guru_id' => 'Semua guru yang dipilih sudah ditugaskan piket di hari ' . $hari])
                ->withInput();
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, $id)
    {
        $guruPiket = GuruPiket::findOrFail($id);

        $request->validate([
            'guru_id' => 'required|exists:m_gurus,id',
            'hari' => 'required|in:Senin,Selasa,Rabu,Kamis,Jumat',
        ], [
            'guru_id.required' => 'Guru wajib dipilih',
            'guru_id.exists' => 'Guru tidak valid',
            'hari.required' => 'Hari wajib dipilih',
            'hari.in' => 'Hari tidak valid',
        ]);

        // Check if assignment already exists (excluding current record)
        $exists = GuruPiket::where('guru_id', $request->guru_id)
            ->where('hari', $request->hari)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return redirect()->back()
                ->withErrors(['guru_id' => 'Guru ini sudah ditugaskan piket di hari ' . $request->hari])
                ->withInput();
        }

        $oldGuruId = $guruPiket->guru_id;

        $guruPiket->update([
            'guru_id' => $request->guru_id,
            'hari' => $request->hari,
            'is_active' => $request->has('is_active'),
        ]);

        // If guru changed, handle role for old guru
        if ($oldGuruId != $request->guru_id) {
            $this->removePiketRoleIfNoAssignments($oldGuruId);
            $this->addPiketRoleToGuru($request->guru_id);
        }

        return redirect()->route('admin.guru-piket-data.index')
            ->with('success', 'Data guru piket berhasil diperbarui!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $guruPiket = GuruPiket::findOrFail($id);
        $guruId = $guruPiket->guru_id;

        $guruPiket->delete();

        // Remove "Piket" role if no other assignments
        $this->removePiketRoleIfNoAssignments($guruId);

        return redirect()->route('admin.guru-piket-data.index')
            ->with('success', 'Data guru piket berhasil dihapus!');
    }

    /**
     * Add "Piket" role to guru's user.
     */
    private function addPiketRoleToGuru($guruId)
    {
        $guru = Guru::with('user')->find($guruId);
        $piketRole = Role::where('nama_role', 'Piket')->first();

        if ($guru && $guru->user && $piketRole) {
            $guru->user->roles()->syncWithoutDetaching([$piketRole->id]);
        }
    }

    /**
     * Remove "Piket" role from guru's user if they have no other piket assignments.
     */
    private function removePiketRoleIfNoAssignments($guruId)
    {
        $hasOtherAssignments = GuruPiket::where('guru_id', $guruId)->exists();

        if (!$hasOtherAssignments) {
            $guru = Guru::with('user')->find($guruId);
            $piketRole = Role::where('nama_role', 'Piket')->first();

            if ($guru && $guru->user && $piketRole) {
                $guru->user->roles()->detach($piketRole->id);
            }
        }
    }
}
