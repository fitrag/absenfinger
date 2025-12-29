<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Kelas;
use App\Models\Jurusan;
use App\Models\MUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StudentController extends Controller
{
    /**
     * Display student list.
     */
    public function index(Request $request)
    {
        $query = Student::with(['kelas', 'jurusan']);

        // Filter by kelas
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        // Filter by status (only apply if status is explicitly set to '0' or '1')
        if ($request->filled('status')) {
            $query->where('is_active', $request->status);
        }

        // Search by NIS, NISN or name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                    ->orWhere('nisn', 'like', "%{$search}%")
                    ->orWhere('name', 'like', "%{$search}%")
                    ->orWhere('finger_id', 'like', "%{$search}%");
            });
        }
        // Pagination
        $perPage = $request->get('perPage', 36);
        if ($perPage === 'all') {
            $students = $query->orderBy('name')->get();
        } else {
            $students = $query->orderBy('name')
                ->paginate((int) $perPage)->withQueryString();
        }

        // Get kelas and jurusan for filter
        $kelasList = Kelas::orderBy('nm_kls')->get();
        $jurusanList = Jurusan::orderBy('paket_keahlian')->get();

        // Statistics
        $totalStudents = Student::count();
        $activeStudents = Student::where('is_active', true)->count();

        return view('admin.students.index', compact(
            'students',
            'kelasList',
            'jurusanList',
            'totalStudents',
            'activeStudents'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $kelasList = Kelas::orderBy('nm_kls')->get();
        $jurusanList = Jurusan::orderBy('paket_keahlian')->get();

        return view('admin.students.create', compact('kelasList', 'jurusanList'));
    }

    /**
     * Store new student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'finger_id' => 'required|string|max:50|unique:students',
            'nis' => 'required|string|max:50|unique:students|unique:users,username',
            'nisn' => 'required|string|max:20',
            'name' => 'required|string|max:255',
            'tmpt_lhr' => 'nullable|string|max:50',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:20',
            'almt_siswa' => 'nullable|string',
            'no_tlp' => 'nullable|string|max:20',
            'nm_ayah' => 'nullable|string|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
            'm_jurusan_id' => 'nullable|exists:m_jurusan,id',
            'is_active' => 'boolean',
        ], [
            'nis.unique' => 'NIS sudah digunakan',
            'nisn.required' => 'NISN wajib diisi (digunakan sebagai password)',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated) {
            // Create user account for student
            $user = MUser::create([
                'name' => $validated['name'],
                'username' => $validated['nis'],
                'password' => $validated['nisn'],
                'level' => 'siswa',
                'is_active' => $validated['is_active'] ?? true,
            ]);

            // Create student with user_id
            $validated['user_id'] = $user->id;
            Student::create($validated);
        });

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan. Akun user otomatis dibuat dengan username: NIS, password: NISN');
    }

    /**
     * Show student detail.
     */
    public function show(Student $student)
    {
        $student->load([
            'kelas',
            'jurusan',
            'attendances' => function ($query) {
                $query->orderBy('checktime', 'desc')->limit(20);
            }
        ]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show edit form.
     */
    public function edit(Student $student)
    {
        $kelasList = Kelas::orderBy('nm_kls')->get();
        $jurusanList = Jurusan::orderBy('paket_keahlian')->get();

        return view('admin.students.edit', compact('student', 'kelasList', 'jurusanList'));
    }

    /**
     * Update student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'finger_id' => 'required|string|max:50|unique:students,finger_id,' . $student->id,
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'nisn' => 'nullable|string|max:20',
            'name' => 'required|string|max:255',
            'tmpt_lhr' => 'nullable|string|max:50',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:20',
            'almt_siswa' => 'nullable|string',
            'no_tlp' => 'nullable|string|max:20',
            'nm_ayah' => 'nullable|string|max:100',
            'kelas_id' => 'nullable|exists:kelas,id',
            'm_jurusan_id' => 'nullable|exists:m_jurusan,id',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        // Update user name if student has associated user
        if ($student->user_id) {
            MUser::where('id', $student->user_id)->update([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'],
            ]);
        } else {
            // If no user_id, try to find by NIS (username)
            MUser::where('username', $student->nis)->update([
                'name' => $validated['name'],
                'is_active' => $validated['is_active'],
            ]);
        }

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Delete student.
     */
    public function destroy(Student $student)
    {
        // Delete associated user based on user_id or NIS
        if ($student->user_id) {
            MUser::where('id', $student->user_id)->delete();
        } else {
            MUser::where('username', $student->nis)->delete();
        }

        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa dan akun user berhasil dihapus.');
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $data = [
            ['finger_id', 'nis', 'nisn', 'name', 'tmpt_lhr', 'tgl_lhr', 'jen_kel', 'agama', 'almt_siswa', 'no_tlp', 'nm_ayah', 'kelas_id', 'm_jurusan_id'],
            ['001', '2024001', '1234567890', 'Nama Siswa', 'Jakarta', '2005-01-15', 'L', 'Islam', 'Jl. Contoh No. 1', '08123456789', 'Nama Ayah', '1', '1'],
        ];

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('template_import_siswa.xlsx');
        exit;
    }

    /**
     * Import students from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:2048',
        ], [
            'file.required' => 'File wajib diunggah',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 2MB',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        $imported = 0;
        $errors = [];
        $rows = [];

        // Parse file based on extension
        if (in_array($extension, ['xlsx', 'xls'])) {
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($path)) {
                $rows = $xlsx->rows();
            } else {
                return redirect()->route('admin.students.index')
                    ->with('error', 'Gagal membaca file Excel: ' . \Shuchkin\SimpleXLSX::parseError());
            }
        } else {
            if (($handle = fopen($path, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        // Skip header row
        $header = array_shift($rows);

        foreach ($rows as $index => $data) {
            $rowNum = $index + 2;

            if (count($data) < 4) {
                $errors[] = "Baris {$rowNum}: Data tidak lengkap";
                continue;
            }

            $finger_id = trim($data[0] ?? '');
            $nis = trim($data[1] ?? '');
            $nisn = trim($data[2] ?? '');
            $name = trim($data[3] ?? '');
            $tmpt_lhr = trim($data[4] ?? '') ?: null;
            $tgl_lhr = trim($data[5] ?? '') ?: null;
            $jen_kel = trim($data[6] ?? '') ?: null;
            $agama = trim($data[7] ?? '') ?: null;
            $almt_siswa = trim($data[8] ?? '') ?: null;
            $no_tlp = trim($data[9] ?? '') ?: null;
            $nm_ayah = trim($data[10] ?? '') ?: null;
            $kelas_id = trim($data[11] ?? '') ?: null;
            $m_jurusan_id = trim($data[12] ?? '') ?: null;

            // Validation
            if (empty($finger_id) || empty($nis) || empty($nisn) || empty($name)) {
                $errors[] = "Baris {$rowNum}: finger_id, nis, nisn, dan name wajib diisi";
                continue;
            }

            // Check duplicates
            if (Student::where('finger_id', $finger_id)->exists()) {
                $errors[] = "Baris {$rowNum}: Finger ID '{$finger_id}' sudah ada";
                continue;
            }
            if (Student::where('nis', $nis)->exists()) {
                $errors[] = "Baris {$rowNum}: NIS '{$nis}' sudah ada";
                continue;
            }
            if (MUser::where('username', $nis)->exists()) {
                $errors[] = "Baris {$rowNum}: Username '{$nis}' sudah ada";
                continue;
            }

            try {
                DB::transaction(function () use ($finger_id, $nis, $nisn, $name, $tmpt_lhr, $tgl_lhr, $jen_kel, $agama, $almt_siswa, $no_tlp, $nm_ayah, $kelas_id, $m_jurusan_id) {
                    // Create user
                    $user = MUser::create([
                        'name' => $name,
                        'username' => $nis,
                        'password' => $nisn,
                        'level' => 'siswa',
                        'is_active' => true,
                    ]);

                    // Create student
                    Student::create([
                        'finger_id' => $finger_id,
                        'nis' => $nis,
                        'nisn' => $nisn,
                        'name' => $name,
                        'tmpt_lhr' => $tmpt_lhr,
                        'tgl_lhr' => $tgl_lhr,
                        'jen_kel' => $jen_kel,
                        'agama' => $agama,
                        'almt_siswa' => $almt_siswa,
                        'no_tlp' => $no_tlp,
                        'nm_ayah' => $nm_ayah,
                        'kelas_id' => $kelas_id ?: null,
                        'm_jurusan_id' => $m_jurusan_id ?: null,
                        'user_id' => $user->id,
                        'is_active' => true,
                    ]);
                });
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }

        $message = "Berhasil mengimport {$imported} data siswa.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " data gagal diimport.";
            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('import_errors', array_slice($errors, 0, 10));
        }

        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }

    /**
     * Show naik kelas form.
     */
    public function naikKelasForm()
    {
        $kelasList = Kelas::orderBy('nm_kls')->get();

        return view('admin.students.naik-kelas', compact('kelasList'));
    }

    /**
     * Get students by kelas for AJAX.
     */
    public function getStudentsByKelasForNaikKelas($kelasId)
    {
        $students = Student::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'nis', 'name']);

        return response()->json($students);
    }

    /**
     * Process naik kelas - per student update.
     */
    public function naikKelas(Request $request)
    {
        $request->validate([
            'students' => 'required|array|min:1',
            'students.*.nis' => 'required|exists:students,nis',
            'students.*.kelas_id' => 'required|exists:kelas,id',
        ], [
            'students.required' => 'Tidak ada data siswa',
            'students.min' => 'Minimal satu siswa harus dipilih',
        ]);

        $updated = 0;
        foreach ($request->students as $data) {
            $student = Student::where('nis', $data['nis'])->first();
            if ($student && $student->kelas_id != $data['kelas_id']) {
                $student->update(['kelas_id' => $data['kelas_id']]);
                $updated++;
            }
        }

        return redirect()->route('admin.students.index')
            ->with('success', "Berhasil mengupdate kelas untuk {$updated} siswa");
    }

    /**
     * Export students to Excel.
     */
    public function export(Request $request)
    {
        $query = Student::with(['kelas', 'jurusan']);

        // Apply filters
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        $students = $query->orderBy('name')->get();

        // Build Excel data
        $data = [
            ['No', 'Finger ID', 'NIS', 'NISN', 'Nama', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Agama', 'Alamat', 'No. Telepon', 'Nama Ayah', 'Kelas', 'Jurusan', 'Status']
        ];

        foreach ($students as $index => $student) {
            $data[] = [
                $index + 1,
                $student->finger_id,
                $student->nis,
                $student->nisn ?? '',
                $student->name,
                $student->jen_kel == 'L' ? 'Laki-laki' : ($student->jen_kel == 'P' ? 'Perempuan' : ''),
                $student->tmpt_lhr ?? '',
                $student->tgl_lhr ? $student->tgl_lhr->format('Y-m-d') : '',
                $student->agama ?? '',
                $student->almt_siswa ?? '',
                $student->no_tlp ?? '',
                $student->nm_ayah ?? '',
                $student->kelas->nm_kls ?? '',
                $student->jurusan->paket_keahlian ?? '',
                $student->is_active ? 'Aktif' : 'Tidak Aktif',
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('data_siswa_' . date('Y-m-d_His') . '.xlsx');
        exit;
    }

    /**
     * Print class attendance sheet PDF.
     */
    public function printAbsensi(Request $request)
    {
        $kelasId = $request->get('kelas_id');
        $tanggal = $request->get('tanggal', date('Y-m-d'));

        if (!$kelasId) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Pilih kelas terlebih dahulu untuk mencetak absensi.');
        }

        $kelas = Kelas::find($kelasId);
        if (!$kelas) {
            return redirect()->route('admin.students.index')
                ->with('error', 'Kelas tidak ditemukan.');
        }

        $students = Student::where('kelas_id', $kelasId)
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // Get wali kelas for this class
        $walas = \App\Models\Walas::with('guru')
            ->where('kelas_id', $kelasId)
            ->first();

        // Get settings as key-value
        $settings = (object) [
            'school_name' => \App\Models\Setting::get('school_name', 'SMK NEGERI 1'),
            'city' => \App\Models\Setting::get('city', 'Seputih Agung'),
        ];

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('admin.students.print-absensi', compact(
            'students',
            'kelas',
            'settings',
            'walas',
            'tanggal'
        ))->setOptions([
                    'isHtml5ParserEnabled' => true,
                    'isRemoteEnabled' => true,
                ]);

        // Set paper with custom margins (top, right, bottom, left in points - 1cm = 28.35pt)
        $pdf->setPaper([0, 0, 595.28, 841.89]); // A4 in points
        $pdf->getDomPDF()->getOptions()->set('defaultFont', 'Arial');

        return $pdf->stream("absensi_kelas_{$kelas->nm_kls}.pdf");
    }
}
