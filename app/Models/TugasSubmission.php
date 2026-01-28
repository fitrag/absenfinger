<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TugasSubmission extends Model
{
    protected $table = 'tugas_submissions';

    protected $fillable = [
        'tugas_id',
        'student_id',
        'file_path',
        'keterangan',
        'submitted_at',
        'nilai',
    ];

    protected $casts = [
        'submitted_at' => 'datetime',
    ];

    public function tugas(): BelongsTo
    {
        return $this->belongsTo(Tugas::class, 'tugas_id');
    }

    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
}
