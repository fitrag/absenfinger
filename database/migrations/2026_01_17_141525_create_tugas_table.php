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
        Schema::create('tugas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->foreignId('tp_id')->constrained('m_tp')->onDelete('cascade');
            $table->string('semester');
            $table->foreignId('mapel_id')->constrained('m_mapels')->onDelete('cascade');
            $table->string('judul');
            $table->text('keterangan')->nullable();
            $table->string('file_path')->nullable();
            $table->date('tanggal_deadline');
            $table->time('jam_deadline');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tugas');
    }
};
