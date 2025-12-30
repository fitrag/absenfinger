<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklHardNilai extends Model
{
    protected $table = 'pkl_hardnilai';

    protected $fillable = [
        'student_id',
        'dudi_id',
        'm_tp_id',
        'pkl_komphard_id',
        'nilai',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }

    public function dudi()
    {
        return $this->belongsTo(\App\Models\Dudi::class, 'dudi_id');
    }

    public function komponenHard()
    {
        return $this->belongsTo(PklKompHard::class, 'pkl_komphard_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'm_tp_id');
    }
}
