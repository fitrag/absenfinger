<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('guru_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');

            // Data Identitas
            $table->string('nik', 20)->nullable()->comment('NIK');
            $table->string('gelar', 50)->nullable()->comment('Gelar');
            $table->string('nm_ibu_kandung', 100)->nullable()->comment('Nama Ibu Kandung');

            // Data Alamat
            $table->string('alamat_jln', 255)->nullable()->comment('Alamat Jalan');
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('nama_dusun', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->decimal('lintang', 10, 7)->nullable()->comment('Latitude');
            $table->decimal('bujur', 10, 7)->nullable()->comment('Longitude');

            // Data KK & Pajak
            $table->string('no_kk', 20)->nullable()->comment('Nomor Kartu Keluarga');
            $table->string('agama', 20)->nullable();
            $table->string('npwp', 30)->nullable();
            $table->string('nm_wajib_pajak', 100)->nullable();
            $table->string('kewarganegaraan', 50)->nullable();

            // Data Pernikahan
            $table->string('status_perkawinan', 30)->nullable();
            $table->string('nm_istri_suami', 100)->nullable();
            $table->string('nip_istri_suami', 30)->nullable();
            $table->string('pekerjaan_istri_suami', 100)->nullable();

            // Data Kepegawaian
            $table->string('status_pegawai', 50)->nullable();
            $table->string('niy', 30)->nullable()->comment('Nomor Induk Yayasan');
            $table->string('jenis_ptk', 50)->nullable()->comment('Jenis PTK');
            $table->string('sk_pengangkatan', 100)->nullable();
            $table->date('tmt_pengangkatan')->nullable()->comment('TMT Pengangkatan');
            $table->string('lembaga_pengangkat', 100)->nullable();
            $table->string('sk_cpns', 100)->nullable();
            $table->date('tmt_tugas_pns')->nullable()->comment('TMT Tugas PNS');
            $table->string('pangkat', 50)->nullable();
            $table->string('sumber_gaji', 50)->nullable();
            $table->string('karpeg', 50)->nullable()->comment('Kartu Pegawai');
            $table->string('kartu_karis', 50)->nullable()->comment('Kartu Karis/Karsu');

            // Data Kontak
            $table->string('no_hp', 20)->nullable();
            $table->string('email', 100)->nullable();

            $table->timestamps();

            // Index
            $table->unique('m_guru_id');
            $table->index('nik');
            $table->index('no_kk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_details');
    }
};
