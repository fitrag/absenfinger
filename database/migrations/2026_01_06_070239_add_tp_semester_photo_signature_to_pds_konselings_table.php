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
        Schema::table('pds_konselings', function (Blueprint $table) {
            $table->foreignId('tp_id')->nullable()->after('status')->constrained('m_tp')->onDelete('set null');
            $table->string('semester')->nullable()->after('tp_id'); // Ganjil/Genap
            $table->string('foto_bukti')->nullable()->after('keterangan'); // Path to evidence photo
            $table->text('ttd_siswa')->nullable()->after('foto_bukti'); // Base64 signature image
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pds_konselings', function (Blueprint $table) {
            $table->dropForeign(['tp_id']);
            $table->dropColumn(['tp_id', 'semester', 'foto_bukti', 'ttd_siswa']);
        });
    }
};
