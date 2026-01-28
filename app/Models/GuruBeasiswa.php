<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruBeasiswa extends Model
{
    use HasFactory;

    protected $fillable = ['m_guru_id', 'jenis', 'ket', 'thn_mulai', 'thn_akhir', 'masih_menerima'];

    protected $casts = ['masih_menerima' => 'boolean'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
