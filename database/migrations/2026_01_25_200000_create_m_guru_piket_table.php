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
        Schema::create('m_guru_piket', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->enum('hari', ['Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']);
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Unique constraint: same guru can't be assigned to same day twice
            $table->unique(['guru_id', 'hari']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_guru_piket');
    }
};
