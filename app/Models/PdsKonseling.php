<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdsKonseling extends Model
{
    use HasFactory;

    protected $table = 'pds_konselings';

    protected $fillable = [
        'student_id',
        'tanggal',
        'permasalahan',
        'penanganan',
        'hasil',
        'keterangan',
        'foto_bukti',
        'ttd_siswa',
        'status',
        'tp_id',
        'semester',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get the student that owns this counseling record.
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the user who created this record.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(MUser::class, 'created_by');
    }

    /**
     * Get status badge class.
     */
    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'bg-amber-500/20 text-amber-400 border-amber-500/30',
            'diproses' => 'bg-blue-500/20 text-blue-400 border-blue-500/30',
            'selesai' => 'bg-emerald-500/20 text-emerald-400 border-emerald-500/30',
            default => 'bg-slate-500/20 text-slate-400 border-slate-500/30',
        };
    }

    /**
     * Get status label.
     */
    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'pending' => 'Pending',
            'diproses' => 'Diproses',
            'selesai' => 'Selesai',
            default => $this->status,
        };
    }
}
