<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PdsPelanggaran extends Model
{
    use HasFactory;

    protected $table = 'pds_pelanggarans';

    protected $fillable = [
        'student_id',
        'tanggal',
        'jenis_pelanggaran',
        'poin',
        'deskripsi',
        'tindakan',
        'keterangan',
        'status',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'poin' => 'integer',
    ];

    /**
     * Get the student that owns this violation record.
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

    /**
     * Get poin badge class based on severity.
     */
    public function getPoinBadgeAttribute(): string
    {
        if ($this->poin >= 50) {
            return 'bg-red-500/20 text-red-400 border-red-500/30';
        } elseif ($this->poin >= 25) {
            return 'bg-amber-500/20 text-amber-400 border-amber-500/30';
        } else {
            return 'bg-slate-500/20 text-slate-400 border-slate-500/30';
        }
    }
}
