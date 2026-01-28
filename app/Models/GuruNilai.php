<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruNilai extends Model
{
    protected $fillable = [
        'guru_id',
        'mapel_id',
        'kelas_id',
        'tp_id',
        'semester',
        'harian_ke',
        'keterangan',
        'status',
    ];

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function kelas()
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function tp()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tp_id');
    }

    public function details()
    {
        return $this->hasMany(GuruNilaiDetail::class, 'guru_nilai_id');
    }
}
