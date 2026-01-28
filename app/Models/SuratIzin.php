<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SuratIzin extends Model
{
    use HasFactory;

    protected $table = 'surat_izins';

    protected $fillable = [
        'student_id',
        'pkl_id',
        'tanggal_mulai',
        'tanggal_selesai',
        'jenis_izin',
        'keterangan',
        'file_path',
        'status',
        'catatan_guru',
        'approved_by',
        'approved_at',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai' => 'date',
        'approved_at' => 'datetime',
    ];

    /**
     * Get the student that owns this surat izin
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the PKL record
     */
    public function pkl(): BelongsTo
    {
        return $this->belongsTo(Pkl::class);
    }

    /**
     * Get the guru who approved
     */
    public function approver(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'approved_by');
    }

    /**
     * Get jenis izin label
     */
    public function getJenisIzinLabelAttribute(): string
    {
        return match ($this->jenis_izin) {
            'sakit' => 'Sakit',
            'izin' => 'Izin',
            'lainnya' => 'Lainnya',
            default => $this->jenis_izin,
        };
    }

    /**
     * Get status label
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Menunggu',
            'disetujui' => 'Disetujui',
            'ditolak' => 'Ditolak',
            default => $this->status,
        };
    }

    /**
     * Get status color class
     */
    public function getStatusColorAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'amber',
            'disetujui' => 'emerald',
            'ditolak' => 'rose',
            default => 'slate',
        };
    }

    /**
     * Get jumlah hari izin
     */
    public function getJumlahHariAttribute(): int
    {
        return $this->tanggal_mulai->diffInDays($this->tanggal_selesai) + 1;
    }
}
