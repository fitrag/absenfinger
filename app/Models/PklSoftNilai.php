<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklSoftNilai extends Model
{
    protected $table = 'pkl_softnilai';

    protected $fillable = [
        'student_id',
        'dudi_id',
        'm_tp_id',
        'pkl_kompsoft_id',
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

    public function komponenSoft()
    {
        return $this->belongsTo(PklKompSoft::class, 'pkl_kompsoft_id');
    }

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'm_tp_id');
    }
}
