<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Guru;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function index()
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::with([
            'guru.detail',
            'guru.sertifikasis',
            'guru.pendidikans',
            'guru.kompetensis',
            'guru.anaks',
            'guru.beasiswas',
            'guru.bukus',
            'guru.diklats',
            'guru.karyaTuliss',
            'guru.kesejahteraans',
            'guru.tunjangans',
            'guru.tugasTambahans',
            'guru.inpasings',
            'guru.gajiBerkalas',
            'guru.karirGurus',
            'guru.jabatans',
            'guru.pangkatGols',
            'guru.jabatanFungsionals',
            'student.detail',
            'student.kelas',
            'student.jurusan'
        ])->findOrFail($userId);

        return view('admin.profile.index', compact('user'));
    }

    /**
     * Update the user's profile.
     */
    public function update(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($user->id)],
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($user->id)],
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        $user->username = $validated['username'];

        // Handle foto upload
        if ($request->hasFile('foto')) {
            // Delete old foto if exists
            if ($user->foto && Storage::disk('public')->exists($user->foto)) {
                Storage::disk('public')->delete($user->foto);
            }

            $fotoPath = $request->file('foto')->store('profile-photos', 'public');
            $user->foto = $fotoPath;
        }

        $user->save();

        // Update session data
        Session::put('user_name', $user->name);
        Session::put('user_foto', $user->foto);

        return redirect()->back()->with('success', 'Profile berhasil diperbarui.');
    }

    /**
     * Update the user's password.
     */
    public function updatePassword(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        // Check current password
        if (!Hash::check($validated['current_password'], $user->password)) {
            return redirect()->back()->withErrors(['current_password' => 'Password saat ini tidak valid.']);
        }

        $user->password = $validated['password'];
        $user->save();

        return redirect()->back()->with('success', 'Password berhasil diperbarui.');
    }

    /**
     * Update guru personal data.
     */
    public function updateGuru(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        // Check if user is guru
        if ($user->level !== 'guru') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'nip' => 'nullable|string|max:50',
            'nuptk' => 'nullable|string|max:50',
            'nama' => 'required|string|max:255',
            'tmpt_lhr' => 'nullable|string|max:100',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'no_tlp' => 'nullable|string|max:20',
            // Detail fields
            'detail.gelar' => 'nullable|string|max:50',
            'detail.nik' => 'nullable|string|max:20',
            'detail.agama' => 'nullable|string|max:20',
            'detail.status_perkawinan' => 'nullable|string|max:30',
            'detail.nm_ibu_kandung' => 'nullable|string|max:100',
            'detail.no_hp' => 'nullable|string|max:20',
            'detail.email' => 'nullable|email|max:100',
            'detail.alamat_jln' => 'nullable|string|max:255',
            'detail.rt' => 'nullable|string|max:5',
            'detail.rw' => 'nullable|string|max:5',
            'detail.nama_dusun' => 'nullable|string|max:50',
            'detail.kelurahan' => 'nullable|string|max:50',
            'detail.kecamatan' => 'nullable|string|max:50',
            'detail.kode_pos' => 'nullable|string|max:10',
            'detail.status_pegawai' => 'nullable|string|max:50',
            'detail.jenis_ptk' => 'nullable|string|max:50',
            'detail.niy' => 'nullable|string|max:50',
            'detail.npwp' => 'nullable|string|max:30',
        ]);

        // Find or create guru record
        $guru = Guru::where('user_id', $userId)->first();

        $guruData = [
            'nip' => $validated['nip'] ?? null,
            'nuptk' => $validated['nuptk'] ?? null,
            'nama' => $validated['nama'],
            'tmpt_lhr' => $validated['tmpt_lhr'] ?? null,
            'tgl_lhr' => $validated['tgl_lhr'] ?? null,
            'jen_kel' => $validated['jen_kel'] ?? null,
            'no_tlp' => $validated['no_tlp'] ?? null,
        ];

        if ($guru) {
            $guru->update($guruData);
        } else {
            $guruData['user_id'] = $userId;
            $guruData['username'] = $user->username;
            $guru = Guru::create($guruData);
        }

        // Update or create guru detail
        if ($request->has('detail')) {
            $detailData = $request->input('detail', []);
            $guru->detail()->updateOrCreate(
                ['m_guru_id' => $guru->id],
                $detailData
            );
        }

        // Also update user name
        $user->name = $validated['nama'];
        $user->save();
        Session::put('user_name', $user->name);

        return redirect()->back()->with('success', 'Data pribadi berhasil diperbarui.');
    }


    /**
     * Update student personal data.
     */
    public function updateStudent(Request $request)
    {
        $userId = Session::get('user_id');

        if (!$userId) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = User::findOrFail($userId);

        // Check if user is siswa
        if ($user->level !== 'siswa') {
            return redirect()->back()->with('error', 'Akses ditolak.');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'nisn' => 'nullable|string|max:20',
            'tmpt_lhr' => 'nullable|string|max:100',
            'tgl_lhr' => 'nullable|date',
            'jen_kel' => 'nullable|in:L,P',
            'agama' => 'nullable|string|max:50',
            'almt_siswa' => 'nullable|string',
            'no_tlp' => 'nullable|string|max:20',
            'nm_ayah' => 'nullable|string|max:100',
            // Detail fields
            'detail.nik' => 'nullable|string|max:20',
            'detail.no_kk' => 'nullable|string|max:20',
            'detail.anak_ke' => 'nullable|integer',
            'detail.jml_sdr_kandung' => 'nullable|integer',
            'detail.hp' => 'nullable|string|max:20',
            'detail.e_mail' => 'nullable|email|max:100',
            'detail.rt' => 'nullable|string|max:5',
            'detail.rw' => 'nullable|string|max:5',
            'detail.dusun' => 'nullable|string|max:50',
            'detail.kelurahan' => 'nullable|string|max:50',
            'detail.kecamatan' => 'nullable|string|max:50',
            'detail.kode_pos' => 'nullable|string|max:10',
            'detail.jns_tinggal' => 'nullable|string|max:50',
            'detail.alt_transp' => 'nullable|string|max:50',
            'detail.ayah_nik' => 'nullable|string|max:20',
            'detail.ayah_th_lhr' => 'nullable|integer',
            'detail.ayah_jenjang' => 'nullable|string|max:20',
            'detail.ayah_pekerjaan' => 'nullable|string|max:50',
            'detail.ayah_penghasilan' => 'nullable|string|max:50',
            'detail.ibu_nama' => 'nullable|string|max:100',
            'detail.ibu_nik' => 'nullable|string|max:20',
            'detail.ibu_th_lahir' => 'nullable|integer',
            'detail.ibu_jenjang' => 'nullable|string|max:20',
            'detail.ibu_pekerjaan' => 'nullable|string|max:50',
            'detail.ibu_penghasilan' => 'nullable|string|max:50',
            'detail.wali_nama' => 'nullable|string|max:100',
            'detail.wali_nik' => 'nullable|string|max:20',
            'detail.wali_pekerjaan' => 'nullable|string|max:50',
            'detail.wali_penghasilan' => 'nullable|string|max:50',
            'detail.sekolah_asal' => 'nullable|string|max:100',
            'detail.skhun' => 'nullable|string|max:50',
            'detail.no_pes_ujian' => 'nullable|string|max:50',
            'detail.no_seri_ijazah' => 'nullable|string|max:50',
            'detail.penerima_kip' => 'nullable|boolean',
            'detail.no_kip' => 'nullable|string|max:50',
            'detail.no_kks' => 'nullable|string|max:50',
            'detail.layak_pip' => 'nullable|boolean',
        ]);

        // Find student record
        $student = Student::where('user_id', $userId)->first();

        $studentData = [
            'name' => $validated['name'],
            'nisn' => $validated['nisn'] ?? null,
            'tmpt_lhr' => $validated['tmpt_lhr'] ?? null,
            'tgl_lhr' => $validated['tgl_lhr'] ?? null,
            'jen_kel' => $validated['jen_kel'] ?? null,
            'agama' => $validated['agama'] ?? null,
            'almt_siswa' => $validated['almt_siswa'] ?? null,
            'no_tlp' => $validated['no_tlp'] ?? null,
            'nm_ayah' => $validated['nm_ayah'] ?? null,
        ];

        if ($student) {
            $student->update($studentData);

            // Update or create student detail
            if ($request->has('detail')) {
                $detailData = $request->input('detail', []);

                // Ensure boolean fields have default values (prevent null constraint violation)
                $detailData['penerima_kip'] = isset($detailData['penerima_kip']) ? (bool) $detailData['penerima_kip'] : false;
                $detailData['layak_pip'] = isset($detailData['layak_pip']) ? (bool) $detailData['layak_pip'] : false;

                $student->detail()->updateOrCreate(
                    ['student_id' => $student->id],
                    $detailData
                );
            }
        }

        // Also update user name
        $user->name = $validated['name'];
        $user->save();
        Session::put('user_name', $user->name);

        return redirect()->back()->with('success', 'Data pribadi berhasil diperbarui.');
    }
}
