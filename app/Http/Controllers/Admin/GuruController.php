<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Guru;
use App\Models\GuruDetail;
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
     * Show guru detail.
     */
    public function show(Guru $guru)
    {
        $guru->load([
            'detail',
            'sertifikasis',
            'pendidikans',
            'kompetensis',
            'anaks',
            'beasiswas',
            'bukus',
            'diklats',
            'karyaTuliss',
            'kesejahteraans',
            'tunjangans',
            'tugasTambahans',
            'inpasings',
            'gajiBerkalas',
            'karirGurus',
            'jabatans',
            'pangkatGols',
            'jabatanFungsionals',
        ]);
        return view('admin.guru.show', compact('guru'));
    }


    /**
     * Show edit form.
     */
    public function edit(Guru $guru)
    {
        $guru->load('detail');
        return view('admin.guru.edit', compact('guru'));
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
            // Detail fields validation
            'detail.nik' => 'nullable|string|max:20',
            'detail.gelar' => 'nullable|string|max:50',
            'detail.nm_ibu_kandung' => 'nullable|string|max:100',
            'detail.alamat_jln' => 'nullable|string|max:255',
            'detail.rt' => 'nullable|string|max:5',
            'detail.rw' => 'nullable|string|max:5',
            'detail.nama_dusun' => 'nullable|string|max:100',
            'detail.kelurahan' => 'nullable|string|max:100',
            'detail.kecamatan' => 'nullable|string|max:100',
            'detail.kode_pos' => 'nullable|string|max:10',
            'detail.lintang' => 'nullable|numeric',
            'detail.bujur' => 'nullable|numeric',
            'detail.no_kk' => 'nullable|string|max:20',
            'detail.agama' => 'nullable|string|max:20',
            'detail.npwp' => 'nullable|string|max:30',
            'detail.nm_wajib_pajak' => 'nullable|string|max:100',
            'detail.kewarganegaraan' => 'nullable|string|max:50',
            'detail.status_perkawinan' => 'nullable|string|max:30',
            'detail.nm_istri_suami' => 'nullable|string|max:100',
            'detail.nip_istri_suami' => 'nullable|string|max:30',
            'detail.pekerjaan_istri_suami' => 'nullable|string|max:100',
            'detail.status_pegawai' => 'nullable|string|max:50',
            'detail.niy' => 'nullable|string|max:30',
            'detail.jenis_ptk' => 'nullable|string|max:50',
            'detail.sk_pengangkatan' => 'nullable|string|max:100',
            'detail.tmt_pengangkatan' => 'nullable|date',
            'detail.lembaga_pengangkat' => 'nullable|string|max:100',
            'detail.sk_cpns' => 'nullable|string|max:100',
            'detail.tmt_tugas_pns' => 'nullable|date',
            'detail.pangkat' => 'nullable|string|max:50',
            'detail.sumber_gaji' => 'nullable|string|max:50',
            'detail.karpeg' => 'nullable|string|max:50',
            'detail.kartu_karis' => 'nullable|string|max:50',
            'detail.no_hp' => 'nullable|string|max:20',
            'detail.email' => 'nullable|email|max:100',
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

            // Update guru basic info
            $guruData = collect($validated)->except(['detail'])->toArray();
            $guru->update($guruData);

            // Update or create guru detail
            if ($request->has('detail')) {
                $detailData = $request->input('detail', []);
                $guru->detail()->updateOrCreate(
                    ['m_guru_id' => $guru->id],
                    $detailData
                );
            }

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
     * Export gurus to Excel with multiple sheets.
     */
    public function export(Request $request)
    {
        // Load all gurus with relationships
        $gurus = Guru::with([
            'detail',
            'anaks',
            'diklats',
            'gajiBerkalas',
            'inpasings',
            'jabatans',
            'jabatanFungsionals',
            'karirGurus',
            'karyaTuliss',
            'kesejahteraans',
            'kompetensis',
            'pangkatGols',
            'pendidikans',
            'sertifikasis',
            'tugasTambahans',
            'tunjangans',
        ])->orderBy('nama')->get();

        // Sheet 1: Data Guru + Detail (Combined)
        $guruData = [
            [
                'No',
                'Username',
                'NIP',
                'NUPTK',
                'Nama',
                'Tempat Lahir',
                'Tanggal Lahir',
                'Jenis Kelamin',
                'No. Telepon',
                // Detail fields
                'NIK',
                'Gelar',
                'Nama Ibu Kandung',
                'Alamat',
                'RT',
                'RW',
                'Dusun',
                'Kelurahan',
                'Kecamatan',
                'Kode Pos',
                'No. KK',
                'Agama',
                'NPWP',
                'Nama Wajib Pajak',
                'Kewarganegaraan',
                'Status Perkawinan',
                'Nama Istri/Suami',
                'NIP Istri/Suami',
                'Pekerjaan Istri/Suami',
                'Status Pegawai',
                'NIY',
                'Jenis PTK',
                'SK Pengangkatan',
                'TMT Pengangkatan',
                'Lembaga Pengangkat',
                'SK CPNS',
                'TMT Tugas PNS',
                'Pangkat',
                'Sumber Gaji',
                'Karpeg',
                'Kartu Karis',
                'No. HP',
                'Email'
            ]
        ];
        foreach ($gurus as $index => $guru) {
            $d = $guru->detail;
            $guruData[] = [
                $index + 1,
                $guru->username ?? '',
                $guru->nip ?? '',
                $guru->nuptk ?? '',
                $guru->nama ?? '',
                $guru->tmpt_lhr ?? '',
                $guru->tgl_lhr ? $guru->tgl_lhr : '',
                $guru->jen_kel ?? '',
                $guru->no_tlp ?? '',
                // Detail fields
                $d->nik ?? '',
                $d->gelar ?? '',
                $d->nm_ibu_kandung ?? '',
                $d->alamat_jln ?? '',
                $d->rt ?? '',
                $d->rw ?? '',
                $d->nama_dusun ?? '',
                $d->kelurahan ?? '',
                $d->kecamatan ?? '',
                $d->kode_pos ?? '',
                $d->no_kk ?? '',
                $d->agama ?? '',
                $d->npwp ?? '',
                $d->nm_wajib_pajak ?? '',
                $d->kewarganegaraan ?? '',
                $d->status_perkawinan ?? '',
                $d->nm_istri_suami ?? '',
                $d->nip_istri_suami ?? '',
                $d->pekerjaan_istri_suami ?? '',
                $d->status_pegawai ?? '',
                $d->niy ?? '',
                $d->jenis_ptk ?? '',
                $d->sk_pengangkatan ?? '',
                $d->tmt_pengangkatan ?? '',
                $d->lembaga_pengangkat ?? '',
                $d->sk_cpns ?? '',
                $d->tmt_tugas_pns ?? '',
                $d->pangkat ?? '',
                $d->sumber_gaji ?? '',
                $d->karpeg ?? '',
                $d->kartu_karis ?? '',
                $d->no_hp ?? '',
                $d->email ?? '',
            ];
        }

        // Sheet 2: Anak
        $anakData = [['No', 'Nama Guru', 'NIP', 'Nama Anak', 'Jenis Kelamin', 'Tempat Lahir', 'Tanggal Lahir', 'Status', 'Pendidikan']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->anaks as $anak) {
                $anakData[] = [$no++, $guru->nama, $guru->nip, $anak->nama ?? '', $anak->jen_kel ?? '', $anak->tmpt_lhr ?? '', $anak->tgl_lhr ?? '', $anak->status ?? '', $anak->pendidikan ?? ''];
            }
        }

        // Sheet 4: Diklat
        $diklatData = [['No', 'Nama Guru', 'NIP', 'Jenis Diklat', 'Nama Diklat', 'Penyelenggara', 'Tahun', 'Peran', 'Jam']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->diklats as $d) {
                $diklatData[] = [$no++, $guru->nama, $guru->nip, $d->jenis ?? '', $d->nama ?? '', $d->penyelenggara ?? '', $d->tahun ?? '', $d->peran ?? '', $d->jam ?? ''];
            }
        }

        // Sheet 5: Gaji Berkala
        $gajiBerkalaData = [['No', 'Nama Guru', 'NIP', 'SK Gaji Berkala', 'TMT Gaji Berkala', 'Tanggal SK', 'Masa Kerja Gol Tahun', 'Masa Kerja Gol Bulan']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->gajiBerkalas as $g) {
                $gajiBerkalaData[] = [$no++, $guru->nama, $guru->nip, $g->sk_gaji_berkala ?? '', $g->tmt_gaji_berkala ?? '', $g->tgl_sk ?? '', $g->masa_kerja_gol_thn ?? '', $g->masa_kerja_gol_bln ?? ''];
            }
        }

        // Sheet 6: Inpasing
        $inpasingData = [['No', 'Nama Guru', 'NIP', 'SK Inpasing', 'TMT Inpasing', 'Tanggal SK', 'Angka Kredit']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->inpasings as $i) {
                $inpasingData[] = [$no++, $guru->nama, $guru->nip, $i->sk_inpasing ?? '', $i->tmt_inpasing ?? '', $i->tgl_sk ?? '', $i->angka_kredit ?? ''];
            }
        }

        // Sheet 7: Jabatan
        $jabatanData = [['No', 'Nama Guru', 'NIP', 'Jabatan', 'SK Jabatan', 'TMT Jabatan', 'Tanggal SK']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->jabatans as $j) {
                $jabatanData[] = [$no++, $guru->nama, $guru->nip, $j->jabatan ?? '', $j->sk_jabatan ?? '', $j->tmt_jabatan ?? '', $j->tgl_sk ?? ''];
            }
        }

        // Sheet 8: Jabatan Fungsional
        $jabatanFungsionalData = [['No', 'Nama Guru', 'NIP', 'Jabatan Fungsional', 'SK Jabatan', 'TMT Jabatan', 'Tanggal SK']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->jabatanFungsionals as $jf) {
                $jabatanFungsionalData[] = [$no++, $guru->nama, $guru->nip, $jf->jabatan_fungsional ?? '', $jf->sk_jabatan ?? '', $jf->tmt_jabatan ?? '', $jf->tgl_sk ?? ''];
            }
        }

        // Sheet 9: Karir Guru
        $karirGuruData = [['No', 'Nama Guru', 'NIP', 'Jenjang', 'Jenis Lembaga', 'Status Kepegawaian', 'Jenis PTK', 'Lembaga', 'No. SK Kerja', 'Tanggal SK Kerja', 'TMT Kerja', 'TST Kerja', 'Tempat Kerja', 'TTD SK']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->karirGurus as $k) {
                $karirGuruData[] = [$no++, $guru->nama, $guru->nip, $k->jenjang ?? '', $k->jenis_lembaga ?? '', $k->status_kepegawaian ?? '', $k->jenis_ptk ?? '', $k->lembaga ?? '', $k->no_sk_kerja ?? '', $k->tgl_sk_kerja ?? '', $k->tmt_kerja ?? '', $k->tst_kerja ?? '', $k->tempat_kerja ?? '', $k->ttd_sk ?? ''];
            }
        }

        // Sheet 10: Karya Tulis
        $karyaTulisData = [['No', 'Nama Guru', 'NIP', 'Judul', 'Tahun', 'Peran', 'Info']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->karyaTuliss as $kt) {
                $karyaTulisData[] = [$no++, $guru->nama, $guru->nip, $kt->judul ?? '', $kt->tahun ?? '', $kt->peran ?? '', $kt->info ?? ''];
            }
        }

        // Sheet 11: Kesejahteraan
        $kesejahteraanData = [['No', 'Nama Guru', 'NIP', 'Jenis Kesejahteraan', 'Nama', 'Penyelenggara', 'Dari Tahun', 'Sampai Tahun', 'Status']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->kesejahteraans as $ks) {
                $kesejahteraanData[] = [$no++, $guru->nama, $guru->nip, $ks->jenis ?? '', $ks->nama ?? '', $ks->penyelenggara ?? '', $ks->dari_tahun ?? '', $ks->sampai_tahun ?? '', $ks->status ?? ''];
            }
        }

        // Sheet 12: Kompetensi
        $kompetensiData = [['No', 'Nama Guru', 'NIP', 'Bidang Studi', 'Tingkat', 'Jurusan']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->kompetensis as $kp) {
                $kompetensiData[] = [$no++, $guru->nama, $guru->nip, $kp->bidang_studi ?? '', $kp->tingkat ?? '', $kp->jurusan ?? ''];
            }
        }

        // Sheet 13: Pangkat Golongan
        $pangkatGolData = [['No', 'Nama Guru', 'NIP', 'Pangkat', 'Golongan', 'SK Pangkat', 'TMT Pangkat', 'Tanggal SK', 'Masa Kerja Gol Tahun', 'Masa Kerja Gol Bulan']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->pangkatGols as $pg) {
                $pangkatGolData[] = [$no++, $guru->nama, $guru->nip, $pg->pangkat ?? '', $pg->golongan ?? '', $pg->sk_pangkat ?? '', $pg->tmt_pangkat ?? '', $pg->tgl_sk ?? '', $pg->masa_kerja_gol_thn ?? '', $pg->masa_kerja_gol_bln ?? ''];
            }
        }

        // Sheet 14: Pendidikan
        $pendidikanData = [['No', 'Nama Guru', 'NIP', 'Jenjang', 'Satuan Pendidikan', 'Fakultas', 'Jurusan', 'Tahun Masuk', 'Tahun Lulus', 'NIM', 'No. Ijazah', 'Tanggal Ijazah', 'Nama Kepala Sekolah Ijazah']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->pendidikans as $pd) {
                $pendidikanData[] = [$no++, $guru->nama, $guru->nip, $pd->jenjang ?? '', $pd->satuan_pend ?? '', $pd->fakultas ?? '', $pd->jurusan ?? '', $pd->thn_masuk ?? '', $pd->thn_lulus ?? '', $pd->nim ?? '', $pd->no_ijazah ?? '', $pd->tgl_ijazah ?? '', $pd->nm_kepsek_ijazah ?? ''];
            }
        }

        // Sheet 15: Sertifikasi
        $sertifikasiData = [['No', 'Nama Guru', 'NIP', 'Jenis', 'No. Registrasi', 'Tahun', 'Bidang Studi', 'No. Peserta', 'NRGT', 'Tanggal NRGT']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->sertifikasis as $s) {
                $sertifikasiData[] = [$no++, $guru->nama, $guru->nip, $s->jenis ?? '', $s->no_reg ?? '', $s->tahun ?? '', $s->bidang_studi ?? '', $s->no_peserta ?? '', $s->nrgt ?? '', $s->tgl_nrgt ?? ''];
            }
        }

        // Sheet 16: Tugas Tambahan
        $tugasTambahanData = [['No', 'Nama Guru', 'NIP', 'Jabatan PTK', 'JPM', 'No. SK', 'Tanggal SK', 'TMT Tugas', 'TST Tugas']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->tugasTambahans as $tt) {
                $tugasTambahanData[] = [$no++, $guru->nama, $guru->nip, $tt->jabatan_ptk ?? '', $tt->jpm ?? '', $tt->no_sk ?? '', $tt->tgl_sk ?? '', $tt->tmt_tugas ?? '', $tt->tst_tugas ?? ''];
            }
        }

        // Sheet 17: Tunjangan
        $tunjanganData = [['No', 'Nama Guru', 'NIP', 'Jenis Tunjangan', 'Nama', 'Sumber Dana', 'Dari Tahun', 'Sampai Tahun', 'Nominal', 'Status']];
        $no = 1;
        foreach ($gurus as $guru) {
            foreach ($guru->tunjangans as $tj) {
                $tunjanganData[] = [$no++, $guru->nama, $guru->nip, $tj->jenis ?? '', $tj->nama ?? '', $tj->sumber_dana ?? '', $tj->dari_tahun ?? '', $tj->sampai_tahun ?? '', $tj->nominal ?? '', $tj->status ?? ''];
            }
        }

        // Create workbook with multiple sheets
        $xlsx = new SimpleXLSXGen();
        $xlsx->addSheet($guruData, 'Data Guru');
        $xlsx->addSheet($anakData, 'Anak');
        $xlsx->addSheet($diklatData, 'Diklat');
        $xlsx->addSheet($gajiBerkalaData, 'Gaji Berkala');
        $xlsx->addSheet($inpasingData, 'Inpasing');
        $xlsx->addSheet($jabatanData, 'Jabatan');
        $xlsx->addSheet($jabatanFungsionalData, 'Jabatan Fungsional');
        $xlsx->addSheet($karirGuruData, 'Karir Guru');
        $xlsx->addSheet($karyaTulisData, 'Karya Tulis');
        $xlsx->addSheet($kesejahteraanData, 'Kesejahteraan');
        $xlsx->addSheet($kompetensiData, 'Kompetensi');
        $xlsx->addSheet($pangkatGolData, 'Pangkat Golongan');
        $xlsx->addSheet($pendidikanData, 'Pendidikan');
        $xlsx->addSheet($sertifikasiData, 'Sertifikasi');
        $xlsx->addSheet($tugasTambahanData, 'Tugas Tambahan');
        $xlsx->addSheet($tunjanganData, 'Tunjangan');

        // Clear any output buffers
        if (ob_get_level()) {
            ob_end_clean();
        }

        $xlsx->downloadAs('data_guru_' . date('Y-m-d_His') . '.xlsx');
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

    /**
     * Get model class based on type.
     */
    private function getRiwayatModel(string $type)
    {
        $models = [
            'sertifikasi' => \App\Models\GuruSertifikasi::class,
            'pendidikan' => \App\Models\GuruPendidikan::class,
            'kompetensi' => \App\Models\GuruKompetensi::class,
            'anak' => \App\Models\GuruAnak::class,
            'beasiswa' => \App\Models\GuruBeasiswa::class,
            'buku' => \App\Models\GuruBuku::class,
            'diklat' => \App\Models\GuruDiklat::class,
            'karya' => \App\Models\GuruKaryaTulis::class,
            'kesejahteraan' => \App\Models\GuruKesejahteraan::class,
            'tunjangan' => \App\Models\GuruTunjangan::class,
            'tugas' => \App\Models\GuruTugasTambahan::class,
            'inpasing' => \App\Models\GuruInpasing::class,
            'gaji' => \App\Models\GuruGajiBerkala::class,
            'karir' => \App\Models\GuruKarirGuru::class,
            'jabatan' => \App\Models\GuruJabatan::class,
            'pangkat' => \App\Models\GuruPangkatGol::class,
            'jab_fungsional' => \App\Models\GuruJabatanFungsional::class,
        ];

        return $models[$type] ?? null;
    }

    /**
     * Store a riwayat record.
     */
    public function storeRiwayat(Request $request, Guru $guru, string $type)
    {
        $modelClass = $this->getRiwayatModel($type);
        if (!$modelClass) {
            return back()->with('error', 'Tipe riwayat tidak valid.');
        }

        $data = $request->except(['_token']);
        $data['m_guru_id'] = $guru->id;

        $modelClass::create($data);

        return back()->with('success', 'Data berhasil ditambahkan.');
    }

    /**
     * Update a riwayat record.
     */
    public function updateRiwayat(Request $request, Guru $guru, string $type, int $id)
    {
        $modelClass = $this->getRiwayatModel($type);
        if (!$modelClass) {
            return back()->with('error', 'Tipe riwayat tidak valid.');
        }

        $record = $modelClass::where('m_guru_id', $guru->id)->findOrFail($id);
        $record->update($request->except(['_token', '_method']));

        return back()->with('success', 'Data berhasil diperbarui.');
    }

    /**
     * Delete a riwayat record.
     */
    public function destroyRiwayat(Guru $guru, string $type, int $id)
    {
        $modelClass = $this->getRiwayatModel($type);
        if (!$modelClass) {
            return back()->with('error', 'Tipe riwayat tidak valid.');
        }

        $record = $modelClass::where('m_guru_id', $guru->id)->findOrFail($id);
        $record->delete();

        return back()->with('success', 'Data berhasil dihapus.');
    }
}

