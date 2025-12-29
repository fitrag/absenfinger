<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruJurnal extends Model
{
    use HasFactory;

    protected $table = 'guru_jurnals';

    protected $fillable = [
        'guru_id',
        'tp_id',
        'semester',
        'tanggal',
        'kelas_id',
        'mapel_id',
        'jam_ke',
        'tmke',
        'absensi',
        'materi',
        'kegiatan',
        'catatan',
    ];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'guru_id');
    }

    public function tp(): BelongsTo
    {
        return $this->belongsTo(TahunPelajaran::class, 'tp_id');
    }

    public function kelas(): BelongsTo
    {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel(): BelongsTo
    {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }
}
