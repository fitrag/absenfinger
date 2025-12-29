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
        Schema::create('guru_attendances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('guru_id')->constrained('m_gurus')->onDelete('cascade');
            $table->date('date');
            $table->string('status')->default('hadir'); // hadir, sakit, izin, alpha
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            // Ensure one attendance record per guru per day
            $table->unique(['guru_id', 'date']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('guru_attendances');
    }
};
