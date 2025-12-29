<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Shuchkin\SimpleXLSX;
use Shuchkin\SimpleXLSXGen;

class GuruController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Guru::query();

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nama', 'like', "%{$search}%")
                    ->orWhere('username', 'like', "%{$search}%")
                    ->orWhere('nip', 'like', "%{$search}%")
                    ->orWhere('nuptk', 'like', "%{$search}%");
            });
        }



        $perPage = $request->get('perPage', 10);
        $gurus = $query->orderBy('nama')->paginate($perPage)->withQueryString();

        return view('admin.guru.index', compact('gurus'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:m_gurus,username',
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'nama' => 'required|string|max:255',
            'tmpt_lhr' => 'nullable|string|max:100',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'no_tlp' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // Create user first
            $user = User::create([
                'name' => $validated['nama'],
                'email' => $validated['username'] . '@guru.local',
                'username' => $validated['username'],
                'password' => Hash::make($validated['username']),
                'level' => 'guru',
                'is_active' => 1,
            ]);

            // Create guru with user_id
            $validated['user_id'] = $user->id;

            Guru::create($validated);

            DB::commit();

            return redirect()->route('admin.guru.index')
                ->with('success', 'Data guru berhasil ditambahkan. Password: ' . $validated['username']);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.guru.index')
                ->with('error', 'Gagal menambahkan data guru: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Guru $guru)
    {
        $validated = $request->validate([
            'username' => ['required', 'string', 'max:255', Rule::unique('m_gurus', 'username')->ignore($guru->id)],
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'nama' => 'required|string|max:255',
            'tmpt_lhr' => 'nullable|string|max:100',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'no_tlp' => 'nullable|string|max:20',
        ]);

        DB::beginTransaction();
        try {
            // Update user name if exists
            if ($guru->user) {
                $guru->user->update([
                    'name' => $validated['nama'],
                    'email' => $validated['username'] . '@guru.local',
                    'username' => $validated['username'],
                ]);
            }

            $guru->update($validated);

            DB::commit();

            return redirect()->route('admin.guru.index')
                ->with('success', 'Data guru berhasil diperbarui.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.guru.index')
                ->with('error', 'Gagal memperbarui data guru: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Guru $guru)
    {
        DB::beginTransaction();
        try {
            $userId = $guru->user_id;
            $guru->delete();

            // Delete associated user
            if ($userId) {
                User::find($userId)?->delete();
            }

            DB::commit();

            return redirect()->route('admin.guru.index')
                ->with('success', 'Data guru berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.guru.index')
                ->with('error', 'Gagal menghapus data guru: ' . $e->getMessage());
        }
    }

    /**
     * Import gurus from Excel file.
     */
    /**
     * Import gurus from Excel file.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls|max:10240',
        ]);

        $file = $request->file('file');

        try {
            if ($xlsx = SimpleXLSX::parse($file->getRealPath())) {
                $rows = $xlsx->rows();
                $headers = array_map(function ($h) {
                    return strtolower(trim($h));
                }, array_shift($rows)); // Extract and normalize headers

                $data = [];
                foreach ($rows as $row) {
                    $rowData = [];
                    foreach ($headers as $index => $header) {
                        $rowData[$header] = $row[$index] ?? null;
                    }
                    $data[] = $rowData;
                }
            } else {
                return redirect()->route('admin.guru.index')
                    ->with('error', 'Gagal memparsing file Excel: ' . SimpleXLSX::parseError());
            }

            $imported = 0;
            $errors = [];

            DB::beginTransaction();

            foreach ($data as $index => $row) {
                $rowNumber = $index + 2; // +2 because headers are row 1 and index starts at 0

                if (empty($row['username']) || empty($row['nama'])) {
                    $errors[] = "Baris {$rowNumber}: Username dan Nama wajib diisi.";
                    continue;
                }

                // Check if username already exists
                if (Guru::where('username', $row['username'])->exists()) {
                    $errors[] = "Baris {$rowNumber}: Username '{$row['username']}' sudah ada.";
                    continue;
                }

                try {
                    // Create user
                    $user = User::create([
                        'name' => $row['nama'],
                        'email' => $row['username'] . '@guru.local',
                        'username' => $row['username'],
                        'password' => Hash::make($row['username']),
                        'level' => 'guru',
                        'is_active' => 1,
                    ]);

                    // Create guru
                    Guru::create([
                        'username' => $row['username'],
                        'nip' => $row['nip'] ?? null,
                        'nuptk' => $row['nuptk'] ?? null,
                        'nama' => $row['nama'],
                        'tmpt_lhr' => $row['tmpt_lhr'] ?? null,
                        'tgl_lhr' => $this->parseDate($row['tgl_lhr'] ?? null),
                        'jen_kel' => $row['jen_kel'] ?? null,
                        'no_tlp' => $row['no_tlp'] ?? null,
                        'user_id' => $user->id,
                    ]);

                    $imported++;
                } catch (\Exception $e) {
                    $errors[] = "Baris {$rowNumber}: " . $e->getMessage();
                }
            }

            DB::commit();

            $message = "Berhasil mengimport {$imported} data guru.";
            if (!empty($errors)) {
                $message .= " " . count($errors) . " baris gagal.";
            }

            return redirect()->route('admin.guru.index')
                ->with('success', $message)
                ->with('import_errors', $errors);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->route('admin.guru.index')
                ->with('error', 'Gagal mengimport data: ' . $e->getMessage());
        }
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $headers = ['username', 'nama', 'nip', 'nuptk', 'tmpt_lhr', 'tgl_lhr', 'jen_kel', 'no_tlp'];
        $sample = ['guru001', 'Nama Guru', '123456789', '9876543210', 'Jakarta', '1990-01-01', 'L', '08123456789'];

        $data = [$headers, $sample];

        $xlsx = SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('template_guru.xlsx');
        exit;
    }

    /**
     * Parse date from Excel (which might be int or string).
     */
    private function parseDate($date)
    {
        if (empty($date))
            return null;

        // If numeric, it's typically Excel date format
        if (is_numeric($date)) {
            $unixDate = ($date - 25569) * 86400;
            return date('Y-m-d', $unixDate);
        }

        try {
            return date('Y-m-d', strtotime($date));
        } catch (\Exception $e) {
            return null;
        }
    }
}
