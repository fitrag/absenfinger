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
            $table->string('keterangan')->nullable()->after('harian_ke');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_nilais', function (Blueprint $table) {
            $table->dropColumn('keterangan');
        });
    }
};
