<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SiswaTerlambat extends Model
{
    use HasFactory;

    protected $table = 'siswa_terlambat';

    protected $fillable = [
        'student_id',
        'tanggal',
        'jam_datang',
        'jam_masuk_seharusnya',
        'keterlambatan_menit',
        'alasan',
        'keterangan',
        'status',
        'tp_id',
        'semester',
        'created_by',
    ];

    protected $casts = [
        'tanggal' => 'date',
    ];

    /**
     * Get the student that owns this late record.
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
     * Calculate lateness in minutes.
     */
    public function hitungKeterlambatan(): int
    {
        $jamMasuk = strtotime($this->jam_masuk_seharusnya);
        $jamDatang = strtotime($this->jam_datang);

        $selisih = ($jamDatang - $jamMasuk) / 60;

        return max(0, (int) $selisih);
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
