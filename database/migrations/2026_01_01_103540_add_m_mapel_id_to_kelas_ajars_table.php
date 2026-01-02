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
        Schema::table('kelas_ajars', function (Blueprint $table) {
            $table->foreignId('m_mapel_id')->nullable()->after('kelas_id')->constrained('m_mapels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('kelas_ajars', function (Blueprint $table) {
            $table->dropForeign(['m_mapel_id']);
            $table->dropColumn('m_mapel_id');
        });
    }
};
