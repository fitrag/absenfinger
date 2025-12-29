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
        Schema::table('guru_nilais', function (Blueprint $table) {
            $table->dropColumn(['tanggal', 'judul', 'keterangan']);
            $table->integer('harian_ke')->after('semester');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_nilais', function (Blueprint $table) {
            $table->date('tanggal')->nullable();
            $table->string('judul')->nullable();
            $table->text('keterangan')->nullable();
            $table->dropColumn('harian_ke');
        });
    }
};
