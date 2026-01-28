<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Tugas extends Model
{
    protected $table = 'tugas';

    protected $fillable = [
        'guru_id',
        'tp_id',
        'semester',
        'mapel_id',
        'judul',
        'keterangan',
        'file_path',
        'tanggal_deadline',
        'jam_deadline',
    ];

    protected $casts = [
        'tanggal_deadline' => 'date',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function tahunPelajaran(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tp_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function kelas(): BelongsToMany
    {
        return $this->belongsToMany(Kelas::class, 'tugas_kelas', 'tugas_id', 'kelas_id')->withTimestamps();
    }

    public function submissions(): HasMany
    {
        return $this->hasMany(TugasSubmission::class, 'tugas_id');
    }

    // Check if deadline has passed
    public function getIsDeadlinePassedAttribute(): bool
    {
        $deadline = $this->tanggal_deadline->format('Y-m-d') . ' ' . $this->jam_deadline;
        return now()->greaterThan($deadline);
    }

    // Get formatted deadline
    public function getDeadlineFormattedAttribute(): string
    {
        return $this->tanggal_deadline->format('d M Y') . ' - ' . substr($this->jam_deadline, 0, 5);
    }
}
