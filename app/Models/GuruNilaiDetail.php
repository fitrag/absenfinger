<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GuruNilaiDetail extends Model
{
    protected $fillable = [
        'guru_nilai_id',
        'student_id',
        'nilai',
    ];

    public function nilaiHeader()
    {
        return $this->belongsTo(GuruNilai::class, 'guru_nilai_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
