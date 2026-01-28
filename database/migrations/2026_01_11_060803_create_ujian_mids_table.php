<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('ujian_mids', function (Blueprint $table) {
            $table->id();
            $table->string('nama_ujian');
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('tp_id');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->date('tanggal');
            $table->time('waktu_mulai');
            $table->time('waktu_selesai');
            $table->integer('durasi')->comment('dalam menit');
            $table->enum('status', ['draft', 'aktif', 'selesai'])->default('draft');
            $table->timestamps();

            $table->foreign('mapel_id')->references('id')->on('m_mapels')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('m_gurus')->onDelete('cascade');
            $table->foreign('tp_id')->references('id')->on('m_tp')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ujian_mids');
    }
};
