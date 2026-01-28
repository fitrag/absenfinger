<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruGajiBerkala extends Model
{
    use HasFactory;

    protected $fillable = ['m_guru_id', 'pangkat_gol', 'nomor_sk', 'tanggal_sk', 'tmt_sk', 'masa_kerja', 'gapok'];

    protected $casts = ['tanggal_sk' => 'date', 'tmt_sk' => 'date', 'gapok' => 'decimal:2'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
