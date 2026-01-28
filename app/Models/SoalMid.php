<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoalMid extends Model
{
    use HasFactory;

    protected $table = 'soal_mids';

    protected $fillable = [
        'file_soal_id',
        'mapel_id',
        'tingkat',
        'guru_id',
        'tp_id',
        'semester',
        'no_soal',
        'pertanyaan',
        'opsi_a',
        'opsi_b',
        'opsi_c',
        'opsi_d',
        'opsi_e',
        'jawaban_benar',
        'bobot',
        'tipe_soal',
    ];

    public function fileSoal()
    {
        return $this->belongsTo(FileSoal::class, 'file_soal_id');
    }

    public function mapel()
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function guru()
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'tp_id');
    }
}
