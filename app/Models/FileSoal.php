<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FileSoal extends Model
{
    use HasFactory;

    protected $table = 'file_soals';

    protected $fillable = [
        'nama_file',
        'file_path',
        'jenis',
        'mapel_id',
        'tingkat',
        'guru_id',
        'tp_id',
        'semester',
        'keterangan',
    ];

    public function mapel()
    {
        return $this->belongsTo(\App\Models\Mapel::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(\App\Models\Guru::class, 'guru_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(\App\Models\TahunPelajaran::class, 'tp_id');
    }

    public function soalMids()
    {
        return $this->hasMany(SoalMid::class, 'file_soal_id');
    }

    public function soalUss()
    {
        return $this->hasMany(SoalUs::class, 'file_soal_id');
    }
}
