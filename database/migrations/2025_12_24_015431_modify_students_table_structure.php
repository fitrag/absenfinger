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
        Schema::table('students', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['class', 'major']);

            // Add new columns
            $table->string('nisn', 20)->nullable()->after('nis');
            $table->string('tmpt_lhr', 50)->nullable()->after('name');
            $table->date('tgl_lhr')->nullable()->after('tmpt_lhr');
            $table->enum('jen_kel', ['L', 'P'])->nullable()->after('tgl_lhr');
            $table->string('agama', 20)->nullable()->after('jen_kel');
            $table->text('almt_siswa')->nullable()->after('agama');
            $table->string('no_tlp', 20)->nullable()->after('almt_siswa');
            $table->string('nm_ayah', 100)->nullable()->after('no_tlp');

            // Add foreign keys
            $table->foreignId('kelas_id')->nullable()->after('nm_ayah')->constrained('kelas')->nullOnDelete();
            $table->foreignId('m_jurusan_id')->nullable()->after('kelas_id')->constrained('m_jurusan')->nullOnDelete();
            $table->foreignId('user_id')->nullable()->after('m_jurusan_id')->constrained('users')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('students', function (Blueprint $table) {
            // Drop foreign keys first
            $table->dropForeign(['kelas_id']);
            $table->dropForeign(['m_jurusan_id']);
            $table->dropForeign(['user_id']);

            // Drop new columns
            $table->dropColumn([
                'nisn',
                'tmpt_lhr',
                'tgl_lhr',
                'jen_kel',
                'agama',
                'almt_siswa',
                'no_tlp',
                'nm_ayah',
                'kelas_id',
                'm_jurusan_id',
                'user_id'
            ]);

            // Restore old columns
            $table->string('class')->nullable()->after('name');
            $table->string('major')->nullable()->after('class');
        });
    }
};
