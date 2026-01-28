<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

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

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function absences()
    {
        return $this->hasMany(GuruAbsence::class, 'guru_id');
    }

    public function detail(): HasOne
    {
        return $this->hasOne(GuruDetail::class, 'm_guru_id');
    }

    // Riwayat relationships
    public function sertifikasis(): HasMany
    {
        return $this->hasMany(GuruSertifikasi::class, 'm_guru_id');
    }

    public function pendidikans(): HasMany
    {
        return $this->hasMany(GuruPendidikan::class, 'm_guru_id');
    }

    public function kompetensis(): HasMany
    {
        return $this->hasMany(GuruKompetensi::class, 'm_guru_id');
    }

    public function anaks(): HasMany
    {
        return $this->hasMany(GuruAnak::class, 'm_guru_id');
    }

    public function beasiswas(): HasMany
    {
        return $this->hasMany(GuruBeasiswa::class, 'm_guru_id');
    }

    public function bukus(): HasMany
    {
        return $this->hasMany(GuruBuku::class, 'm_guru_id');
    }

    public function diklats(): HasMany
    {
        return $this->hasMany(GuruDiklat::class, 'm_guru_id');
    }

    public function karyaTuliss(): HasMany
    {
        return $this->hasMany(GuruKaryaTulis::class, 'm_guru_id');
    }

    public function kesejahteraans(): HasMany
    {
        return $this->hasMany(GuruKesejahteraan::class, 'm_guru_id');
    }

    public function tunjangans(): HasMany
    {
        return $this->hasMany(GuruTunjangan::class, 'm_guru_id');
    }

    public function tugasTambahans(): HasMany
    {
        return $this->hasMany(GuruTugasTambahan::class, 'm_guru_id');
    }

    public function inpasings(): HasMany
    {
        return $this->hasMany(GuruInpasing::class, 'm_guru_id');
    }

    public function gajiBerkalas(): HasMany
    {
        return $this->hasMany(GuruGajiBerkala::class, 'm_guru_id');
    }

    public function karirGurus(): HasMany
    {
        return $this->hasMany(GuruKarirGuru::class, 'm_guru_id');
    }

    public function jabatans(): HasMany
    {
        return $this->hasMany(GuruJabatan::class, 'm_guru_id');
    }

    public function pangkatGols(): HasMany
    {
        return $this->hasMany(GuruPangkatGol::class, 'm_guru_id');
    }

    public function jabatanFungsionals(): HasMany
    {
        return $this->hasMany(GuruJabatanFungsional::class, 'm_guru_id');
    }
}
