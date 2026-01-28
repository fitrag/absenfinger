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
        Schema::table('siswa_terlambat', function (Blueprint $table) {
            $table->foreignId('tp_id')->nullable()->after('status')->constrained('m_tp')->onDelete('set null');
            $table->string('semester')->nullable()->after('tp_id'); // Ganjil/Genap
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('siswa_terlambat', function (Blueprint $table) {
            $table->dropForeign(['tp_id']);
            $table->dropColumn(['tp_id', 'semester']);
        });
    }
};
