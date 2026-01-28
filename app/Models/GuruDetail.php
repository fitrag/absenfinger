<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        // Data Identitas
        'nik',
        'gelar',
        'nm_ibu_kandung',
        // Data Alamat
        'alamat_jln',
        'rt',
        'rw',
        'nama_dusun',
        'kelurahan',
        'kecamatan',
        'kode_pos',
        'lintang',
        'bujur',
        // Data KK & Pajak
        'no_kk',
        'agama',
        'npwp',
        'nm_wajib_pajak',
        'kewarganegaraan',
        // Data Pernikahan
        'status_perkawinan',
        'nm_istri_suami',
        'nip_istri_suami',
        'pekerjaan_istri_suami',
        // Data Kepegawaian
        'status_pegawai',
        'niy',
        'jenis_ptk',
        'sk_pengangkatan',
        'tmt_pengangkatan',
        'lembaga_pengangkat',
        'sk_cpns',
        'tmt_tugas_pns',
        'pangkat',
        'sumber_gaji',
        'karpeg',
        'kartu_karis',
        // Data Kontak
        'no_hp',
        'email',
    ];

    protected $casts = [
        'tmt_pengangkatan' => 'date',
        'tmt_tugas_pns' => 'date',
        'lintang' => 'decimal:7',
        'bujur' => 'decimal:7',
    ];

    /**
     * Get the guru that owns this detail.
     */
    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
