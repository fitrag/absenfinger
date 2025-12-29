<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guru_absences', function (Blueprint $table) {
            // Drop foreign key first
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });

        Schema::table('guru_absences', function (Blueprint $table) {
            // Add JSON column for multiple kelas
            $table->json('kelas_ids')->nullable()->after('status');
        });
    }

    public function down(): void
    {
        Schema::table('guru_absences', function (Blueprint $table) {
            $table->dropColumn('kelas_ids');
        });

        Schema::table('guru_absences', function (Blueprint $table) {
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->onDelete('set null');
        });
    }
};
