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
        Schema::table('pds_pelanggarans', function (Blueprint $table) {
            $table->string('foto_bukti')->nullable()->after('keterangan'); // Path to evidence photo
            $table->text('ttd_siswa')->nullable()->after('foto_bukti'); // Base64 signature image
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pds_pelanggarans', function (Blueprint $table) {
            $table->dropColumn(['foto_bukti', 'ttd_siswa']);
        });
    }
};
