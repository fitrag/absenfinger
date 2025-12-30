<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PklKompHard extends Model
{
    protected $table = 'pkl_komphards';

    protected $fillable = [
        'm_jurusan_id',
        'nama',
    ];

    /**
     * Get the jurusan that owns this komponen hard skill.
     */
    public function jurusan()
    {
        return $this->belongsTo(Jurusan::class, 'm_jurusan_id');
    }
}
