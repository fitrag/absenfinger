<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('guru_jurnals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->date('tanggal');
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
            $table->foreignId('mapel_id')->nullable()->constrained('m_mapels')->onDelete('set null');
            $table->string('jam_ke')->nullable();
            $table->text('materi')->nullable();
            $table->text('kegiatan')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guru_jurnals');
    }
};
