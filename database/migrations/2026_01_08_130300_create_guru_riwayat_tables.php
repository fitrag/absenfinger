<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // 1. Riwayat Sertifikasi
        Schema::create('guru_sertifikasis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jenis_serti', 100)->nullable();
            $table->string('no', 100)->nullable();
            $table->string('thn', 10)->nullable();
            $table->string('bidang_studi', 100)->nullable();
            $table->string('nrg', 50)->nullable();
            $table->string('no_pes', 50)->nullable();
            $table->timestamps();
        });

        // 2. Riwayat Pendidikan Formal
        Schema::create('guru_pendidikans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('bidang_studi', 100)->nullable();
            $table->string('jenjang_pendidikan', 50)->nullable();
            $table->string('gelar', 50)->nullable();
            $table->string('satuan_pendidikan', 150)->nullable();
            $table->string('thn_masuk', 10)->nullable();
            $table->string('nim', 50)->nullable();
            $table->string('mata_kuliah', 100)->nullable();
            $table->string('semester', 10)->nullable();
            $table->decimal('ipk', 4, 2)->nullable();
            $table->timestamps();
        });

        // 3. Kompetensi
        Schema::create('guru_kompetensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('bidang_studi', 100)->nullable();
            $table->integer('urutan')->nullable();
            $table->timestamps();
        });

        // 4. Anak
        Schema::create('guru_anaks', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('nama', 100)->nullable();
            $table->string('status', 50)->nullable();
            $table->string('jenjang', 50)->nullable();
            $table->string('nisn', 20)->nullable();
            $table->char('jk', 1)->nullable();
            $table->string('tmpt_lhr', 100)->nullable();
            $table->date('tgl_lhr')->nullable();
            $table->string('thn_masuk', 10)->nullable();
            $table->timestamps();
        });

        // 5. Beasiswa
        Schema::create('guru_beasiswas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jenis', 100)->nullable();
            $table->text('ket')->nullable();
            $table->string('thn_mulai', 10)->nullable();
            $table->string('thn_akhir', 10)->nullable();
            $table->boolean('masih_menerima')->default(false);
            $table->timestamps();
        });

        // 6. Buku
        Schema::create('guru_bukus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('judul', 255)->nullable();
            $table->string('thn', 10)->nullable();
            $table->string('penerbit', 100)->nullable();
            $table->string('isbn', 30)->nullable();
            $table->timestamps();
        });

        // 7. Diklat
        Schema::create('guru_diklats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jns_diklat', 100)->nullable();
            $table->string('nama', 255)->nullable();
            $table->string('penyelenggara', 150)->nullable();
            $table->string('thn', 10)->nullable();
            $table->string('peran', 50)->nullable();
            $table->string('tingkat', 50)->nullable();
            $table->integer('brp_jam')->nullable();
            $table->string('sertifikat_diklat', 100)->nullable();
            $table->timestamps();
        });

        // 8. Karya Tulis
        Schema::create('guru_karya_tuliss', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('judul', 255)->nullable();
            $table->string('thn_pembuatan', 10)->nullable();
            $table->string('publikasi', 100)->nullable();
            $table->text('ket')->nullable();
            $table->string('url_publikasi', 255)->nullable();
            $table->timestamps();
        });

        // 9. Kesejahteraan
        Schema::create('guru_kesejahteraans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jenis', 100)->nullable();
            $table->string('nama', 150)->nullable();
            $table->string('penyelenggara', 150)->nullable();
            $table->string('dari_th', 10)->nullable();
            $table->string('sampai_th', 10)->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });

        // 10. Tunjangan
        Schema::create('guru_tunjangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jenis', 100)->nullable();
            $table->string('nama', 150)->nullable();
            $table->string('instansi', 150)->nullable();
            $table->string('sk_tunjangan', 100)->nullable();
            $table->date('tgl_sk')->nullable();
            $table->string('semester', 10)->nullable();
            $table->string('sumber_dana', 50)->nullable();
            $table->string('dari_th', 10)->nullable();
            $table->string('sampai_th', 10)->nullable();
            $table->decimal('nominal', 15, 2)->nullable();
            $table->string('status', 50)->nullable();
            $table->timestamps();
        });

        // 11. Tugas Tambahan
        Schema::create('guru_tugas_tambahans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jabatan', 100)->nullable();
            $table->string('no_sk', 100)->nullable();
            $table->date('tmt_tugas')->nullable();
            $table->date('tst_tugas')->nullable();
            $table->timestamps();
        });

        // 12. Inpasing Non PNS
        Schema::create('guru_inpasings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('pangkat_gol', 50)->nullable();
            $table->string('no_sk_inpasing', 100)->nullable();
            $table->date('tgl_sk')->nullable();
            $table->date('tmt_sk')->nullable();
            $table->decimal('angka_kredit', 10, 2)->nullable();
            $table->integer('masa_kerja_thn')->nullable();
            $table->integer('masa_kerja_bln')->nullable();
            $table->timestamps();
        });

        // 13. Riwayat Gaji Berkala
        Schema::create('guru_gaji_berkalas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('pangkat_gol', 50)->nullable();
            $table->string('nomor_sk', 100)->nullable();
            $table->date('tanggal_sk')->nullable();
            $table->date('tmt_sk')->nullable();
            $table->string('masa_kerja', 50)->nullable();
            $table->decimal('gapok', 15, 2)->nullable();
            $table->timestamps();
        });

        // 14. Riwayat Karir Guru
        Schema::create('guru_karir_gurus', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jenjang_pendidikan', 50)->nullable();
            $table->string('jenis_lembaga', 100)->nullable();
            $table->string('satuan_pegawai', 100)->nullable();
            $table->string('jns_ptk', 50)->nullable();
            $table->string('lembaga_pengangkat', 100)->nullable();
            $table->string('no_sk_kerja', 100)->nullable();
            $table->date('tgl_sk_kerja')->nullable();
            $table->date('tmt_kerja')->nullable();
            $table->date('tst_kerja')->nullable();
            $table->string('mapel', 100)->nullable();
            $table->timestamps();
        });

        // 15. Riwayat Jabatan
        Schema::create('guru_jabatans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jabatan_ptk', 100)->nullable();
            $table->string('sk_jabatan', 100)->nullable();
            $table->date('tmt_sk')->nullable();
            $table->timestamps();
        });

        // 16. Riwayat Pangkat Golongan
        Schema::create('guru_pangkat_gols', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('pangkat_gol', 50)->nullable();
            $table->string('no_sk', 100)->nullable();
            $table->date('tgl_pangkat')->nullable();
            $table->date('tmt_pangkat')->nullable();
            $table->integer('masa_kerja_tahun')->nullable();
            $table->integer('masa_kerja_bln')->nullable();
            $table->timestamps();
        });

        // 17. Riwayat Jabatan Fungsional
        Schema::create('guru_jabatan_fungsionals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('m_guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->string('jabatan_fungsional', 100)->nullable();
            $table->string('sk_jabatan', 100)->nullable();
            $table->date('tmt_sk')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_jabatan_fungsionals');
        Schema::dropIfExists('guru_pangkat_gols');
        Schema::dropIfExists('guru_jabatans');
        Schema::dropIfExists('guru_karir_gurus');
        Schema::dropIfExists('guru_gaji_berkalas');
        Schema::dropIfExists('guru_inpasings');
        Schema::dropIfExists('guru_tugas_tambahans');
        Schema::dropIfExists('guru_tunjangans');
        Schema::dropIfExists('guru_kesejahteraans');
        Schema::dropIfExists('guru_karya_tuliss');
        Schema::dropIfExists('guru_diklats');
        Schema::dropIfExists('guru_bukus');
        Schema::dropIfExists('guru_beasiswas');
        Schema::dropIfExists('guru_anaks');
        Schema::dropIfExists('guru_kompetensis');
        Schema::dropIfExists('guru_pendidikans');
        Schema::dropIfExists('guru_sertifikasis');
    }
};
