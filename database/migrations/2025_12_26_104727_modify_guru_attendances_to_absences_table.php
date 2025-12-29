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
        // Rename table
        Schema::rename('guru_attendances', 'guru_absences');

        // Modify columns
        Schema::table('guru_absences', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['check_in', 'check_out']);

            // Rename notes to ket
            $table->renameColumn('notes', 'ket');

            // Add kelas_id
            $table->foreignId('kelas_id')->nullable()->after('date')->constrained('kelas')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('guru_absences', function (Blueprint $table) {
            // Drop kelas_id
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');

            // Rename ket back to notes
            $table->renameColumn('ket', 'notes');

            // Add back old columns
            $table->time('check_in')->nullable();
            $table->time('check_out')->nullable();
        });

        // Rename table back
        Schema::rename('guru_absences', 'guru_attendances');
    }
};
