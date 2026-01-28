<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('hasil_mids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('ujian_mid_id');
            $table->unsignedBigInteger('student_id');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->json('jawaban_siswa')->nullable();
            $table->integer('benar')->nullable();
            $table->integer('salah')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->foreign('ujian_mid_id')->references('id')->on('ujian_mids')->onDelete('cascade');
            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->unique(['ujian_mid_id', 'student_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('hasil_mids');
    }
};
