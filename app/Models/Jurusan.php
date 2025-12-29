<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Jurusan extends Model
{
    protected $table = 'm_jurusan';

    protected $fillable = [
        'bidang',
        'program',
        'paket_keahlian',
    ];
}
