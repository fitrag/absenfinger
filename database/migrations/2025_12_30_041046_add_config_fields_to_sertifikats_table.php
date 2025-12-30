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
        Schema::table('sertifikats', function (Blueprint $table) {
            $table->date('tgl_mulai')->nullable()->after('bgBack');
            $table->date('tgl_selesai')->nullable()->after('tgl_mulai');
            $table->date('tgl_cetak')->nullable()->after('tgl_selesai');
            $table->foreignId('m_tp_id')->nullable()->after('tgl_cetak')->constrained('m_tp')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikats', function (Blueprint $table) {
            $table->dropForeign(['m_tp_id']);
            $table->dropColumn(['tgl_mulai', 'tgl_selesai', 'tgl_cetak', 'm_tp_id']);
        });
    }
};
