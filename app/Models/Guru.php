<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Guru extends Model
{
    use HasFactory;

    protected $table = 'm_gurus';

    protected $fillable = [
        'username',
        'nip',
        'nuptk',
        'nama',
        'tmpt_lhr',
        'tgl_lhr',
        'jen_kel',
        'no_tlp',
        'user_id',
    ];

    /**
     * Get the user associated with the guru.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the absences for the guru.
     */
    public function absences()
    {
        return $this->hasMany(GuruAbsence::class, 'guru_id');
    }
}
