<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilUs extends Model
{
    use HasFactory;

    protected $table = 'hasil_uss';

    protected $fillable = [
        'ujian_us_id',
        'student_id',
        'nilai',
        'jawaban_siswa',
        'benar',
        'salah',
        'catatan',
    ];

    protected $casts = [
        'jawaban_siswa' => 'array',
        'nilai' => 'decimal:2',
    ];

    public function ujianUs()
    {
        return $this->belongsTo(UjianUs::class, 'ujian_us_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
