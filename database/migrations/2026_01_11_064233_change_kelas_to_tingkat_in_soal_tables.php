<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        // Drop foreign key and change kelas_id to tingkat in file_soals
        Schema::table('file_soals', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('file_soals', function (Blueprint $table) {
            $table->enum('tingkat', ['X', 'XI', 'XII'])->after('mapel_id');
        });

        // Drop foreign key and change kelas_id to tingkat in soal_mids
        Schema::table('soal_mids', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('soal_mids', function (Blueprint $table) {
            $table->enum('tingkat', ['X', 'XI', 'XII'])->after('mapel_id');
        });

        // Drop foreign key and change kelas_id to tingkat in soal_uss
        Schema::table('soal_uss', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('soal_uss', function (Blueprint $table) {
            $table->enum('tingkat', ['X', 'XI', 'XII'])->after('mapel_id');
        });

        // Drop foreign key and change kelas_id to tingkat in ujian_mids
        Schema::table('ujian_mids', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('ujian_mids', function (Blueprint $table) {
            $table->enum('tingkat', ['X', 'XI', 'XII'])->after('mapel_id');
        });

        // Drop foreign key and change kelas_id to tingkat in ujian_uss
        Schema::table('ujian_uss', function (Blueprint $table) {
            $table->dropForeign(['kelas_id']);
            $table->dropColumn('kelas_id');
        });
        Schema::table('ujian_uss', function (Blueprint $table) {
            $table->enum('tingkat', ['X', 'XI', 'XII'])->after('mapel_id');
        });
    }

    public function down(): void
    {
        // Revert back to kelas_id for all tables
        $tables = ['file_soals', 'soal_mids', 'soal_uss', 'ujian_mids', 'ujian_uss'];

        foreach ($tables as $tableName) {
            Schema::table($tableName, function (Blueprint $table) {
                $table->dropColumn('tingkat');
            });
            Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                $table->unsignedBigInteger('kelas_id')->after('mapel_id');
                $table->foreign('kelas_id')->references('id')->on('kelas')->onDelete('cascade');
            });
        }
    }
};
