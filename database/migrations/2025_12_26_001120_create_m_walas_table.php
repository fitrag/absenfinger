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
        Schema::create('m_walas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->unique()->constrained('m_gurus')->onDelete('cascade');
            $table->foreignId('kelas_id')->unique()->constrained('kelas')->onDelete('cascade');
            $table->string('tahun_ajaran')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_walas');
    }
};
