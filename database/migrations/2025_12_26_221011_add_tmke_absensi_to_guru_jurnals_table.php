<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guru_jurnals', function (Blueprint $table) {
            $table->string('tmke')->nullable()->after('jam_ke'); // Temu Ke (pertemuan)
            $table->text('absensi')->nullable()->after('tmke');   // Absensi siswa
        });
    }

    public function down(): void
    {
        Schema::table('guru_jurnals', function (Blueprint $table) {
            $table->dropColumn(['tmke', 'absensi']);
        });
    }
};
