<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('soal_mids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('file_soal_id')->nullable();
            $table->unsignedBigInteger('mapel_id');
            $table->unsignedBigInteger('kelas_id');
            $table->unsignedBigInteger('guru_id');
            $table->unsignedBigInteger('tp_id');
            $table->enum('semester', ['Ganjil', 'Genap']);
            $table->integer('no_soal');
            $table->text('pertanyaan');
            $table->text('opsi_a')->nullable();
            $table->text('opsi_b')->nullable();
            $table->text('opsi_c')->nullable();
            $table->text('opsi_d')->nullable();
            $table->text('opsi_e')->nullable();
            $table->string('jawaban_benar')->nullable();
            $table->integer('bobot')->default(1);
            $table->enum('tipe_soal', ['pilihan_ganda', 'essay'])->default('pilihan_ganda');
            $table->timestamps();

            $table->foreign('file_soal_id')->references('id')->on('file_soals')->onDelete('set null');
            $table->foreign('mapel_id')->references('id')->on('m_mapels')->onDelete('cascade');
            $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            $table->foreign('guru_id')->references('id')->on('m_gurus')->onDelete('cascade');
            $table->foreign('tp_id')->references('id')->on('m_tp')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('soal_mids');
    }
};
