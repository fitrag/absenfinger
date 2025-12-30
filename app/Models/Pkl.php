<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Pkl extends Model
{
    use HasFactory;

    protected $table = 'pkls';

    protected $fillable = [
        'student_id',
        'dudi_id',
        'pembimbing_sekolah_id',
        'pembimbing_industri',
        'pimpinan',
        'tp_id',
        'created_by',
    ];

    /**
     * Get the student
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the dudi (tempat PKL)
     */
    public function dudi(): BelongsTo
    {
        return $this->belongsTo(Dudi::class);
    }

    /**
     * Get the school mentor (guru)
     */
    public function pembimbingSekolah(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'pembimbing_sekolah_id');
    }

    /**
     * Get the tahun pelajaran
     */
    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tp_id');
    }

    /**
     * Get the creator
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the sertifikat
     */
    /**
     * Get the sertifikat
     */
    public function sertifikat(): HasOne
    {
        return $this->hasOne(Sertifikat::class);
    }

    /**
     * Get soft skill nilai
     */
    public function softNilai()
    {
        return $this->hasMany(PklSoftNilai::class, 'student_id', 'student_id');
    }

    /**
     * Get hard skill nilai
     */
    public function hardNilai()
    {
        return $this->hasMany(PklHardNilai::class, 'student_id', 'student_id');
    }

    /**
     * Get wirausaha nilai
     */
    public function wirausahaNilai()
    {
        return $this->hasMany(PklWirausahaNilai::class, 'student_id', 'student_id');
    }
}
