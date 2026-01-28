<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruKarirGuru extends Model
{
    use HasFactory;

    protected $fillable = [
        'm_guru_id',
        'jenjang_pendidikan',
        'jenis_lembaga',
        'satuan_pegawai',
        'jns_ptk',
        'lembaga_pengangkat',
        'no_sk_kerja',
        'tgl_sk_kerja',
        'tmt_kerja',
        'tst_kerja',
        'mapel'
    ];

    protected $casts = ['tgl_sk_kerja' => 'date', 'tmt_kerja' => 'date', 'tst_kerja' => 'date'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
