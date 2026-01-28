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
        Schema::create('student_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('student_id')->constrained('students')->onDelete('cascade');

            // Data Identitas
            $table->string('npd', 50)->nullable()->comment('Nomor Pokok Daerah');
            $table->string('nik', 20)->nullable()->comment('NIK Siswa');
            $table->string('no_kk', 20)->nullable()->comment('Nomor Kartu Keluarga');
            $table->string('no_reg_akta_lhr', 50)->nullable()->comment('No Reg Akta Lahir');

            // Data Alamat
            $table->string('rt', 5)->nullable();
            $table->string('rw', 5)->nullable();
            $table->string('dusun', 100)->nullable();
            $table->string('kelurahan', 100)->nullable();
            $table->string('kecamatan', 100)->nullable();
            $table->string('kode_pos', 10)->nullable();
            $table->string('jns_tinggal', 50)->nullable()->comment('Jenis Tinggal');
            $table->string('alt_transp', 50)->nullable()->comment('Alat Transportasi');

            // Data Kontak
            $table->string('telp', 20)->nullable();
            $table->string('hp', 20)->nullable();
            $table->string('e_mail', 100)->nullable();

            // Data Akademik
            $table->string('skhun', 50)->nullable();
            $table->string('no_pes_ujian', 50)->nullable()->comment('Nomor Peserta Ujian');
            $table->string('no_seri_ijazah', 50)->nullable()->comment('Nomor Seri Ijazah');
            $table->string('sekolah_asal', 150)->nullable();
            $table->integer('anak_ke')->nullable();

            // Data Ayah
            $table->string('ayah_nama', 100)->nullable();
            $table->integer('ayah_th_lhr')->nullable()->comment('Tahun Lahir Ayah');
            $table->string('ayah_jenjang', 50)->nullable()->comment('Jenjang Pendidikan Ayah');
            $table->string('ayah_pekerjaan', 100)->nullable();
            $table->string('ayah_penghasilan', 50)->nullable();
            $table->string('ayah_nik', 20)->nullable();

            // Data Ibu
            $table->string('ibu_nama', 100)->nullable();
            $table->integer('ibu_th_lahir')->nullable()->comment('Tahun Lahir Ibu');
            $table->string('ibu_jenjang', 50)->nullable()->comment('Jenjang Pendidikan Ibu');
            $table->string('ibu_pekerjaan', 100)->nullable();
            $table->string('ibu_penghasilan', 50)->nullable();
            $table->string('ibu_nik', 20)->nullable();

            // Data Wali
            $table->string('wali_nama', 100)->nullable();
            $table->integer('wali_th_lahir')->nullable()->comment('Tahun Lahir Wali');
            $table->string('wali_jenjang', 50)->nullable()->comment('Jenjang Pendidikan Wali');
            $table->string('wali_pekerjaan', 100)->nullable();
            $table->string('wali_penghasilan', 50)->nullable();
            $table->string('wali_nik', 20)->nullable();

            // Data Bantuan/KIP
            $table->boolean('p_kps')->default(false)->comment('Penerima KPS');
            $table->boolean('penerima_kip')->default(false)->comment('Penerima KIP');
            $table->string('no_kip', 50)->nullable()->comment('Nomor KIP');
            $table->string('no_kks', 50)->nullable()->comment('Nomor KKS');
            $table->boolean('layak_pip')->default(false)->comment('Layak PIP');
            $table->text('alasan_layak_pip')->nullable()->comment('Alasan Layak PIP');

            // Data Bank
            $table->string('bank', 50)->nullable();
            $table->string('no_rek', 50)->nullable();
            $table->string('an_rek', 100)->nullable()->comment('Atas Nama Rekening');

            // Data Khusus
            $table->string('kebutuhan_khusus', 100)->nullable();

            // Data Fisik
            $table->decimal('berat_bdn', 5, 2)->nullable()->comment('Berat Badan dalam kg');
            $table->decimal('tinggi_bdn', 5, 2)->nullable()->comment('Tinggi Badan dalam cm');
            $table->decimal('lingkar_kep', 5, 2)->nullable()->comment('Lingkar Kepala dalam cm');
            $table->integer('jml_sdr_kandung')->nullable()->comment('Jumlah Saudara Kandung');

            // Data Lokasi
            $table->decimal('lintang', 10, 7)->nullable()->comment('Latitude');
            $table->decimal('bujur', 10, 7)->nullable()->comment('Longitude');
            $table->decimal('jarak_rmh_skul', 6, 2)->nullable()->comment('Jarak Rumah ke Sekolah dalam km');

            $table->timestamps();

            // Index
            $table->unique('student_id');
            $table->index('nik');
            $table->index('no_kk');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_details');
    }
};
