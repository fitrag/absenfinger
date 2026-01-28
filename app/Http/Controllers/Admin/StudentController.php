<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentDetail;
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
                    ->orWhere('name', 'like', "%{$search}%");
            });
        }
        // Pagination
        $perPage = $request->get('perPage', 36);

        // Join with kelas for proper sorting
        $query->select('students.*')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->orderBy('kelas.nm_kls', 'asc')
            ->orderBy('students.name', 'asc');

        if ($perPage === 'all') {
            $students = $query->get();
        } else {
            $students = $query->paginate((int) $perPage)->withQueryString();
        }

        // Get kelas and jurusan for filter (with student count)
        $kelasList = Kelas::withCount('students')
            ->where('nm_kls', '!=', '-')
            ->orderBy('nm_kls')
            ->get();
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
            'detail',
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
        $student->load('detail');
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
            // Detail fields validation
            'detail.npd' => 'nullable|string|max:50',
            'detail.nik' => 'nullable|string|max:20',
            'detail.no_kk' => 'nullable|string|max:20',
            'detail.no_reg_akta_lhr' => 'nullable|string|max:50',
            'detail.rt' => 'nullable|string|max:5',
            'detail.rw' => 'nullable|string|max:5',
            'detail.dusun' => 'nullable|string|max:100',
            'detail.kelurahan' => 'nullable|string|max:100',
            'detail.kecamatan' => 'nullable|string|max:100',
            'detail.kode_pos' => 'nullable|string|max:10',
            'detail.jns_tinggal' => 'nullable|string|max:50',
            'detail.alt_transp' => 'nullable|string|max:50',
            'detail.telp' => 'nullable|string|max:20',
            'detail.hp' => 'nullable|string|max:20',
            'detail.e_mail' => 'nullable|email|max:100',
            'detail.skhun' => 'nullable|string|max:50',
            'detail.no_pes_ujian' => 'nullable|string|max:50',
            'detail.no_seri_ijazah' => 'nullable|string|max:50',
            'detail.sekolah_asal' => 'nullable|string|max:150',
            'detail.anak_ke' => 'nullable|integer|min:1',
            'detail.ayah_nama' => 'nullable|string|max:100',
            'detail.ayah_th_lhr' => 'nullable|integer|min:1900|max:2100',
            'detail.ayah_jenjang' => 'nullable|string|max:50',
            'detail.ayah_pekerjaan' => 'nullable|string|max:100',
            'detail.ayah_penghasilan' => 'nullable|string|max:50',
            'detail.ayah_nik' => 'nullable|string|max:20',
            'detail.ibu_nama' => 'nullable|string|max:100',
            'detail.ibu_th_lahir' => 'nullable|integer|min:1900|max:2100',
            'detail.ibu_jenjang' => 'nullable|string|max:50',
            'detail.ibu_pekerjaan' => 'nullable|string|max:100',
            'detail.ibu_penghasilan' => 'nullable|string|max:50',
            'detail.ibu_nik' => 'nullable|string|max:20',
            'detail.wali_nama' => 'nullable|string|max:100',
            'detail.wali_th_lahir' => 'nullable|integer|min:1900|max:2100',
            'detail.wali_jenjang' => 'nullable|string|max:50',
            'detail.wali_pekerjaan' => 'nullable|string|max:100',
            'detail.wali_penghasilan' => 'nullable|string|max:50',
            'detail.wali_nik' => 'nullable|string|max:20',
            'detail.p_kps' => 'nullable|boolean',
            'detail.penerima_kip' => 'nullable|boolean',
            'detail.no_kip' => 'nullable|string|max:50',
            'detail.no_kks' => 'nullable|string|max:50',
            'detail.layak_pip' => 'nullable|boolean',
            'detail.alasan_layak_pip' => 'nullable|string',
            'detail.bank' => 'nullable|string|max:50',
            'detail.no_rek' => 'nullable|string|max:50',
            'detail.an_rek' => 'nullable|string|max:100',
            'detail.kebutuhan_khusus' => 'nullable|string|max:100',
            'detail.berat_bdn' => 'nullable|numeric|min:0|max:500',
            'detail.tinggi_bdn' => 'nullable|numeric|min:0|max:300',
            'detail.lingkar_kep' => 'nullable|numeric|min:0|max:100',
            'detail.jml_sdr_kandung' => 'nullable|integer|min:0',
            'detail.lintang' => 'nullable|numeric',
            'detail.bujur' => 'nullable|numeric',
            'detail.jarak_rmh_skul' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');

        DB::transaction(function () use ($validated, $student, $request) {
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

            // Update student basic info
            $studentData = collect($validated)->except(['detail'])->toArray();
            $student->update($studentData);

            // Update or create student detail
            if ($request->has('detail')) {
                $detailData = $request->input('detail', []);
                // Convert checkbox values to boolean
                $detailData['p_kps'] = isset($detailData['p_kps']) && $detailData['p_kps'];
                $detailData['penerima_kip'] = isset($detailData['penerima_kip']) && $detailData['penerima_kip'];
                $detailData['layak_pip'] = isset($detailData['layak_pip']) && $detailData['layak_pip'];

                $student->detail()->updateOrCreate(
                    ['student_id' => $student->id],
                    $detailData
                );
            }
        });

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
     * Toggle student gender (L <-> P).
     */
    public function toggleGender(Student $student)
    {
        $newGender = $student->jen_kel === 'L' ? 'P' : 'L';
        $student->update(['jen_kel' => $newGender]);

        return response()->json([
            'success' => true,
            'jen_kel' => $newGender,
            'message' => 'Jenis kelamin berhasil diubah menjadi ' . ($newGender === 'L' ? 'Laki-laki' : 'Perempuan')
        ]);
    }

    /**
     * Download import template.
     */
    public function downloadTemplate()
    {
        $data = [
            ['nis', 'nisn', 'name', 'tmpt_lhr', 'tgl_lhr', 'jen_kel', 'agama', 'almt_siswa', 'no_tlp', 'nm_ayah', 'kelas_id', 'm_jurusan_id'],
            ['2024001', '1234567890', 'Nama Siswa', 'Jakarta', '2005-01-15', 'L', 'Islam', 'Jl. Contoh No. 1', '08123456789', 'Nama Ayah', '1', '1'],
        ];

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('template_import_siswa.xlsx');
        exit;
    }

    /**
     * Download import template for student details.
     */
    public function downloadTemplateDetail()
    {
        // Get all students with class info, ordered by class then name
        $students = Student::select('students.id', 'students.nis', 'students.name', 'kelas.nm_kls')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->orderBy('kelas.nm_kls', 'asc')
            ->orderBy('students.name', 'asc')
            ->get();

        // Header row with StudentDetail fields (added kelas column)
        $header = ['student_id', 'nis', 'name', 'kelas', 'nik', 'no_kk', 'npd', 'no_reg_akta_lhr', 'rt', 'rw', 'dusun', 'kelurahan', 'kecamatan', 'kode_pos', 'jns_tinggal', 'alt_transp', 'telp', 'hp', 'e_mail', 'skhun', 'no_pes_ujian', 'no_seri_ijazah', 'sekolah_asal', 'anak_ke', 'ayah_nama', 'ayah_th_lhr', 'ayah_jenjang', 'ayah_pekerjaan', 'ayah_penghasilan', 'ayah_nik', 'ibu_nama', 'ibu_th_lahir', 'ibu_jenjang', 'ibu_pekerjaan', 'ibu_penghasilan', 'ibu_nik', 'wali_nama', 'wali_th_lahir', 'wali_jenjang', 'wali_pekerjaan', 'wali_penghasilan', 'wali_nik', 'p_kps', 'penerima_kip', 'no_kip', 'no_kks', 'layak_pip', 'alasan_layak_pip'];

        $data = [$header];

        foreach ($students as $student) {
            $data[] = [
                $student->id,
                $student->nis,
                $student->name,
                $student->nm_kls ?? '-',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                ''
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);
        $xlsx->downloadAs('template_import_detail_siswa.xlsx');
        exit;
    }

    /**
     * Import student details from Excel.
     */
    public function importDetail(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:2048',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());

        $imported = 0;
        $errors = [];
        $rows = [];

        if (in_array($extension, ['xlsx', 'xls'])) {
            if ($xlsx = \Shuchkin\SimpleXLSX::parse($path)) {
                $rows = $xlsx->rows();
            } else {
                return redirect()->route('admin.students.index')
                    ->with('error', 'Gagal membaca file Excel');
            }
        } else {
            if (($handle = fopen($path, 'r')) !== false) {
                while (($data = fgetcsv($handle)) !== false) {
                    $rows[] = $data;
                }
                fclose($handle);
            }
        }

        // Skip header
        $header = array_shift($rows);

        foreach ($rows as $index => $data) {
            $rowNum = $index + 2;

            if (count($data) < 3)
                continue;

            $studentId = trim($data[0] ?? '');
            if (empty($studentId))
                continue;

            $student = Student::find($studentId);
            if (!$student) {
                $errors[] = "Baris {$rowNum}: Student ID '{$studentId}' tidak ditemukan";
                continue;
            }

            try {
                StudentDetail::updateOrCreate(
                    ['student_id' => $studentId],
                    [
                        // Note: index 3 is kelas (skipped), detail fields start from index 4
                        'nik' => trim($data[4] ?? '') ?: null,
                        'no_kk' => trim($data[5] ?? '') ?: null,
                        'npd' => trim($data[6] ?? '') ?: null,
                        'no_reg_akta_lhr' => trim($data[7] ?? '') ?: null,
                        'rt' => trim($data[8] ?? '') ?: null,
                        'rw' => trim($data[9] ?? '') ?: null,
                        'dusun' => trim($data[10] ?? '') ?: null,
                        'kelurahan' => trim($data[11] ?? '') ?: null,
                        'kecamatan' => trim($data[12] ?? '') ?: null,
                        'kode_pos' => trim($data[13] ?? '') ?: null,
                        'jns_tinggal' => trim($data[14] ?? '') ?: null,
                        'alt_transp' => trim($data[15] ?? '') ?: null,
                        'telp' => trim($data[16] ?? '') ?: null,
                        'hp' => trim($data[17] ?? '') ?: null,
                        'e_mail' => trim($data[18] ?? '') ?: null,
                        'skhun' => trim($data[19] ?? '') ?: null,
                        'no_pes_ujian' => trim($data[20] ?? '') ?: null,
                        'no_seri_ijazah' => trim($data[21] ?? '') ?: null,
                        'sekolah_asal' => trim($data[22] ?? '') ?: null,
                        'anak_ke' => trim($data[23] ?? '') ?: null,
                        'ayah_nama' => trim($data[24] ?? '') ?: null,
                        'ayah_th_lhr' => trim($data[25] ?? '') ?: null,
                        'ayah_jenjang' => trim($data[26] ?? '') ?: null,
                        'ayah_pekerjaan' => trim($data[27] ?? '') ?: null,
                        'ayah_penghasilan' => trim($data[28] ?? '') ?: null,
                        'ayah_nik' => trim($data[29] ?? '') ?: null,
                        'ibu_nama' => trim($data[30] ?? '') ?: null,
                        'ibu_th_lahir' => trim($data[31] ?? '') ?: null,
                        'ibu_jenjang' => trim($data[32] ?? '') ?: null,
                        'ibu_pekerjaan' => trim($data[33] ?? '') ?: null,
                        'ibu_penghasilan' => trim($data[34] ?? '') ?: null,
                        'ibu_nik' => trim($data[35] ?? '') ?: null,
                        'wali_nama' => trim($data[36] ?? '') ?: null,
                        'wali_th_lahir' => trim($data[37] ?? '') ?: null,
                        'wali_jenjang' => trim($data[38] ?? '') ?: null,
                        'wali_pekerjaan' => trim($data[39] ?? '') ?: null,
                        'wali_penghasilan' => trim($data[40] ?? '') ?: null,
                        'wali_nik' => trim($data[41] ?? '') ?: null,
                        'p_kps' => trim($data[42] ?? '') ?: null,
                        'penerima_kip' => trim($data[43] ?? '') ?: null,
                        'no_kip' => trim($data[44] ?? '') ?: null,
                        'no_kks' => trim($data[45] ?? '') ?: null,
                        'layak_pip' => trim($data[46] ?? '') ?: null,
                        'alasan_layak_pip' => trim($data[47] ?? '') ?: null,
                    ]
                );
                $imported++;
            } catch (\Exception $e) {
                $errors[] = "Baris {$rowNum}: " . $e->getMessage();
            }
        }

        $message = "Import detail selesai. {$imported} data berhasil diimport.";
        if (count($errors) > 0) {
            $message .= " " . count($errors) . " data gagal.";
            return redirect()->route('admin.students.index')
                ->with('success', $message)
                ->with('import_errors', array_slice($errors, 0, 10));
        }

        return redirect()->route('admin.students.index')->with('success', $message);
    }

    /**
     * Import students from Excel.
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls,csv,txt|max:2048',
            'import_mode' => 'required|in:skip,update',
        ], [
            'file.required' => 'File wajib diunggah',
            'file.mimes' => 'File harus berformat Excel (xlsx, xls) atau CSV',
            'file.max' => 'Ukuran file maksimal 2MB',
            'import_mode.required' => 'Mode import wajib dipilih',
        ]);

        $file = $request->file('file');
        $path = $file->getRealPath();
        $extension = strtolower($file->getClientOriginalExtension());
        $importMode = $request->input('import_mode', 'skip');

        $imported = 0;
        $updated = 0;
        $skipped = 0;
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

            // Skip completely empty rows
            $allEmpty = true;
            foreach ($data as $cell) {
                if (!empty(trim($cell ?? ''))) {
                    $allEmpty = false;
                    break;
                }
            }
            if ($allEmpty) {
                continue;
            }

            if (count($data) < 3) {
                $errors[] = "Baris {$rowNum}: Data tidak lengkap (hanya " . count($data) . " kolom)";
                continue;
            }

            // Column mapping (no finger_id):
            // 0: nis, 1: nisn, 2: name, 3: tmpt_lhr, 4: tgl_lhr, 5: jen_kel, 
            // 6: agama, 7: almt_siswa, 8: no_tlp, 9: nm_ayah, 10: kelas_id, 11: m_jurusan_id
            $nis = trim($data[0] ?? '');
            $nisn = trim($data[1] ?? '');
            $name = trim($data[2] ?? '');
            $tmpt_lhr = trim($data[3] ?? '') ?: null;
            $tgl_lhr = trim($data[4] ?? '') ?: null;
            $jen_kel = trim($data[5] ?? '') ?: null;
            $agama = trim($data[6] ?? '') ?: null;
            $almt_siswa = trim($data[7] ?? '') ?: null;
            $no_tlp = trim($data[8] ?? '') ?: null;
            $nm_ayah = trim($data[9] ?? '') ?: null;
            $kelas_id = trim($data[10] ?? '') ?: null;
            $m_jurusan_id = trim($data[11] ?? '') ?: null;

            // Sanitize: remove BOM, hidden characters, and extra whitespace
            $nis = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/u', '', $nis);
            $nisn = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/u', '', $nisn);
            $name = preg_replace('/[\x00-\x1F\x7F\xEF\xBB\xBF]/u', '', $name);

            // Additional trim after sanitization
            $nis = trim($nis);
            $nisn = trim($nisn);
            $name = trim($name);

            // Validation with detailed debug info
            $missingFields = [];
            if (empty($nis))
                $missingFields[] = 'nis';
            if (empty($nisn))
                $missingFields[] = 'nisn';
            if (empty($name))
                $missingFields[] = 'name';

            if (!empty($missingFields)) {
                $debugInfo = "Data dibaca: [" . implode('|', array_map(function ($v) {
                    return "'" . substr($v ?? '', 0, 20) . "'";
                }, array_slice($data, 0, 4))) . "]";
                $errors[] = "Baris {$rowNum}: " . implode(', ', $missingFields) . " kosong. {$debugInfo}";
                continue;
            }

            // Check if student exists by NIS
            $existingStudent = Student::whereRaw('TRIM(nis) = ?', [$nis])->first();

            if ($existingStudent) {
                if ($importMode === 'skip') {
                    $skipped++;
                    continue;
                } else {
                    // Update mode
                    try {
                        DB::transaction(function () use ($existingStudent, $nis, $nisn, $name, $tmpt_lhr, $tgl_lhr, $jen_kel, $agama, $almt_siswa, $no_tlp, $nm_ayah, $kelas_id, $m_jurusan_id) {
                            // Update student
                            $existingStudent->update([
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
                            ]);

                            // Update user name if exists
                            if ($existingStudent->user_id) {
                                MUser::where('id', $existingStudent->user_id)->update(['name' => $name]);
                            }
                        });
                        $updated++;
                    } catch (\Exception $e) {
                        $errors[] = "Baris {$rowNum}: Gagal update - " . $e->getMessage();
                    }
                    continue;
                }
            }

            // Check if username already exists (for new student)
            $existingUser = MUser::whereRaw('TRIM(username) = ?', [$nis])->first();
            if ($existingUser) {
                $errors[] = "Baris {$rowNum}: Username '{$nis}' sudah ada (User ID: {$existingUser->id})";
                continue;
            }

            try {
                DB::transaction(function () use ($nis, $nisn, $name, $tmpt_lhr, $tgl_lhr, $jen_kel, $agama, $almt_siswa, $no_tlp, $nm_ayah, $kelas_id, $m_jurusan_id) {
                    // Create user
                    $user = MUser::create([
                        'name' => $name,
                        'username' => $nis,
                        'password' => $nisn,
                        'level' => 'siswa',
                        'is_active' => true,
                    ]);

                    // Create student (no finger_id)
                    Student::create([
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

        // Build result message
        $messages = [];
        if ($imported > 0)
            $messages[] = "{$imported} data baru diimport";
        if ($updated > 0)
            $messages[] = "{$updated} data diupdate";
        if ($skipped > 0)
            $messages[] = "{$skipped} data diskip";

        $message = "Import selesai. " . implode(', ', $messages) . ".";

        if (count($errors) > 0) {
            $message .= " " . count($errors) . " data gagal diproses.";
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
        $deactivated = 0;

        foreach ($request->students as $data) {
            $student = Student::where('nis', $data['nis'])->first();
            if ($student && $student->kelas_id != $data['kelas_id']) {
                // Get the new kelas
                $newKelas = Kelas::find($data['kelas_id']);

                // Check if new kelas is "-" (alumni/graduated)
                if ($newKelas && $newKelas->nm_kls === '-') {
                    // Deactivate student
                    $student->update([
                        'kelas_id' => $data['kelas_id'],
                        'is_active' => false,
                    ]);

                    // Deactivate associated user
                    if ($student->user_id) {
                        MUser::where('id', $student->user_id)->update(['is_active' => false]);
                    } else {
                        // Fallback: try to find user by NIS
                        MUser::where('username', $student->nis)->update(['is_active' => false]);
                    }

                    $deactivated++;
                } else {
                    // Normal class update
                    $student->update(['kelas_id' => $data['kelas_id']]);
                }
                $updated++;
            }
        }

        $message = "Berhasil mengupdate kelas untuk {$updated} siswa";
        if ($deactivated > 0) {
            $message .= ". {$deactivated} siswa dinonaktifkan (lulus/pindah ke kelas '-')";
        }

        return redirect()->route('admin.students.index')
            ->with('success', $message);
    }

    /**
     * Export students to Excel.
     */
    public function export(Request $request)
    {
        $query = Student::with(['kelas', 'jurusan', 'detail']);

        // Apply filters
        if ($request->has('kelas_id') && $request->kelas_id) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Order by kelas first, then by name
        $students = $query->select('students.*')
            ->leftJoin('kelas', 'students.kelas_id', '=', 'kelas.id')
            ->orderBy('kelas.nm_kls', 'asc')
            ->orderBy('students.name', 'asc')
            ->get();

        // Build Excel data with detail fields
        $data = [
            [
                'No',
                'Kelas',
                'NIS',
                'NISN',
                'Nama',
                'Jenis Kelamin',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Agama',
                'Alamat',
                'No. Telepon',
                'Nama Ayah',
                'Jurusan',
                'Status',
                // Detail fields
                'NIK',
                'No. KK',
                'NPD',
                'No. Reg Akta Lahir',
                'RT',
                'RW',
                'Dusun',
                'Kelurahan',
                'Kecamatan',
                'Kode Pos',
                'Jenis Tinggal',
                'Alat Transportasi',
                'Telepon',
                'HP',
                'Email',
                'SKHUN',
                'No. Peserta Ujian',
                'No. Seri Ijazah',
                'Sekolah Asal',
                'Anak Ke',
                'Nama Ayah',
                'Tahun Lahir Ayah',
                'Jenjang Pendidikan Ayah',
                'Pekerjaan Ayah',
                'Penghasilan Ayah',
                'NIK Ayah',
                'Nama Ibu',
                'Tahun Lahir Ibu',
                'Jenjang Pendidikan Ibu',
                'Pekerjaan Ibu',
                'Penghasilan Ibu',
                'NIK Ibu',
                'Nama Wali',
                'Tahun Lahir Wali',
                'Jenjang Pendidikan Wali',
                'Pekerjaan Wali',
                'Penghasilan Wali',
                'NIK Wali',
                'Penerima KPS',
                'Penerima KIP',
                'No. KIP',
                'No. KKS',
                'Layak PIP',
                'Alasan Layak PIP',
                // Data Bank
                'Nama Bank',
                'No. Rekening',
                'Atas Nama Rekening',
                // Data Khusus & Fisik
                'Kebutuhan Khusus',
                'Berat Badan',
                'Tinggi Badan',
                'Lingkar Kepala',
                'Jumlah Saudara Kandung',
                // Data Lokasi
                'Lintang',
                'Bujur',
                'Jarak Rumah ke Sekolah'
            ]
        ];

        foreach ($students as $index => $student) {
            $detail = $student->detail;
            $data[] = [
                $index + 1,
                $student->kelas->nm_kls ?? '',
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
                $student->jurusan->paket_keahlian ?? '',
                $student->is_active ? 'Aktif' : 'Tidak Aktif',
                // Detail fields
                $detail->nik ?? '',
                $detail->no_kk ?? '',
                $detail->npd ?? '',
                $detail->no_reg_akta_lhr ?? '',
                $detail->rt ?? '',
                $detail->rw ?? '',
                $detail->dusun ?? '',
                $detail->kelurahan ?? '',
                $detail->kecamatan ?? '',
                $detail->kode_pos ?? '',
                $detail->jns_tinggal ?? '',
                $detail->alt_transp ?? '',
                $detail->telp ?? '',
                $detail->hp ?? '',
                $detail->e_mail ?? '',
                $detail->skhun ?? '',
                $detail->no_pes_ujian ?? '',
                $detail->no_seri_ijazah ?? '',
                $detail->sekolah_asal ?? '',
                $detail->anak_ke ?? '',
                $detail->ayah_nama ?? '',
                $detail->ayah_th_lhr ?? '',
                $detail->ayah_jenjang ?? '',
                $detail->ayah_pekerjaan ?? '',
                $detail->ayah_penghasilan ?? '',
                $detail->ayah_nik ?? '',
                $detail->ibu_nama ?? '',
                $detail->ibu_th_lahir ?? '',
                $detail->ibu_jenjang ?? '',
                $detail->ibu_pekerjaan ?? '',
                $detail->ibu_penghasilan ?? '',
                $detail->ibu_nik ?? '',
                $detail->wali_nama ?? '',
                $detail->wali_th_lahir ?? '',
                $detail->wali_jenjang ?? '',
                $detail->wali_pekerjaan ?? '',
                $detail->wali_penghasilan ?? '',
                $detail->wali_nik ?? '',
                ($detail && $detail->p_kps) ? 'Ya' : 'Tidak',
                ($detail && $detail->penerima_kip) ? 'Ya' : 'Tidak',
                $detail->no_kip ?? '',
                $detail->no_kks ?? '',
                ($detail && $detail->layak_pip) ? 'Ya' : 'Tidak',
                $detail->alasan_layak_pip ?? '',
                // Data Bank
                $detail->bank ?? '',
                $detail->no_rek ?? '',
                $detail->an_rek ?? '',
                // Data Khusus & Fisik
                $detail->kebutuhan_khusus ?? '',
                $detail->berat_bdn ?? '',
                $detail->tinggi_bdn ?? '',
                $detail->lingkar_kep ?? '',
                $detail->jml_sdr_kandung ?? '',
                // Data Lokasi
                $detail->lintang ?? '',
                $detail->bujur ?? '',
                $detail->jarak_rmh_skul ?? '',
            ];
        }

        $xlsx = \Shuchkin\SimpleXLSXGen::fromArray($data);

        // Clear any output buffers to prevent file corruption
        if (ob_get_level()) {
            ob_end_clean();
        }

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
