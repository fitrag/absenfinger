<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guru_absences', function (Blueprint $table) {
            $table->string('jam_ke')->nullable()->after('kelas_ids');
        });
    }

    public function down(): void
    {
        Schema::table('guru_absences', function (Blueprint $table) {
            $table->dropColumn('jam_ke');
        });
    }
};
