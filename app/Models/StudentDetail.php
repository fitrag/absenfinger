<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StudentDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        // Data Identitas
        'npd',
        'nik',
        'no_kk',
        'no_reg_akta_lhr',
        // Data Alamat
        'rt',
        'rw',
        'dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'jns_tinggal',
        'alt_transp',
        // Data Kontak
        'telp',
        'hp',
        'e_mail',
        // Data Akademik
        'skhun',
        'no_pes_ujian',
        'no_seri_ijazah',
        'sekolah_asal',
        'anak_ke',
        // Data Ayah
        'ayah_nama',
        'ayah_th_lhr',
        'ayah_jenjang',
        'ayah_pekerjaan',
        'ayah_penghasilan',
        'ayah_nik',
        // Data Ibu
        'ibu_nama',
        'ibu_th_lahir',
        'ibu_jenjang',
        'ibu_pekerjaan',
        'ibu_penghasilan',
        'ibu_nik',
        // Data Wali
        'wali_nama',
        'wali_th_lahir',
        'wali_jenjang',
        'wali_pekerjaan',
        'wali_penghasilan',
        'wali_nik',
        // Data Bantuan/KIP
        'p_kps',
        'penerima_kip',
        'no_kip',
        'no_kks',
        'layak_pip',
        'alasan_layak_pip',
        // Data Bank
        'bank',
        'no_rek',
        'an_rek',
        // Data Khusus
        'kebutuhan_khusus',
        // Data Fisik
        'berat_bdn',
        'tinggi_bdn',
        'lingkar_kep',
        'jml_sdr_kandung',
        // Data Lokasi
        'lintang',
        'bujur',
        'jarak_rmh_skul',
    ];

    protected $casts = [
        'p_kps' => 'boolean',
        'penerima_kip' => 'boolean',
        'layak_pip' => 'boolean',
        'ayah_th_lhr' => 'integer',
        'ibu_th_lahir' => 'integer',
        'wali_th_lahir' => 'integer',
        'anak_ke' => 'integer',
        'jml_sdr_kandung' => 'integer',
        'berat_bdn' => 'decimal:2',
        'tinggi_bdn' => 'decimal:2',
        'lingkar_kep' => 'decimal:2',
        'lintang' => 'decimal:7',
        'bujur' => 'decimal:7',
        'jarak_rmh_skul' => 'decimal:2',
    ];

    /**
     * Get the student that owns this detail.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }
}
