<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UjianUs extends Model
{
    use HasFactory;

    protected $table = 'ujian_uss';

    protected $fillable = [
        'nama_ujian',
        'mapel_id',
        'tingkat',
        'guru_id',
        'tp_id',
        'semester',
        'tanggal',
        'waktu_mulai',
        'waktu_selesai',
        'durasi',
        'status',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

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

    public function hasilUss()
    {
        return $this->hasMany(HasilUs::class, 'ujian_us_id');
    }

    public function soalUss()
    {
        return $this->hasMany(SoalUs::class, 'mapel_id', 'mapel_id')
            ->where('tingkat', $this->tingkat)
            ->where('tp_id', $this->tp_id)
            ->where('semester', $this->semester);
    }
}
