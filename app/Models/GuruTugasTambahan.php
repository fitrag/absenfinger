<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class GuruTugasTambahan extends Model
{
    use HasFactory;

    protected $fillable = ['m_guru_id', 'jabatan', 'no_sk', 'tmt_tugas', 'tst_tugas'];

    protected $casts = ['tmt_tugas' => 'date', 'tst_tugas' => 'date'];

    public function guru(): BelongsTo
    {
        return $this->belongsTo(Guru::class, 'm_guru_id');
    }
}
