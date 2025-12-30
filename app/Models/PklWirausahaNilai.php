<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklWirausahaNilai extends Model
{
    protected $table = 'pkl_wirausahanilais';

    protected $fillable = [
        'student_id',
        'dudi_id',
        'm_tp_id',
        'pkl_kompwirausaha_id',
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

    public function tahunPelajaran()
    {
        return $this->belongsTo(TahunPelajaran::class, 'm_tp_id');
    }

    public function komponenWirausaha()
    {
        return $this->belongsTo(PklKompWirausaha::class, 'pkl_kompwirausaha_id');
    }
}
