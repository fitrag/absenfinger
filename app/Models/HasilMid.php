<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HasilMid extends Model
{
    use HasFactory;

    protected $table = 'hasil_mids';

    protected $fillable = [
        'ujian_mid_id',
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

    public function ujianMid()
    {
        return $this->belongsTo(UjianMid::class, 'ujian_mid_id');
    }

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
