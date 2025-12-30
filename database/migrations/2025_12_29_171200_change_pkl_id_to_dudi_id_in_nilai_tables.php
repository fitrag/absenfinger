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
        // Update pkl_softnilai
        Schema::table('pkl_softnilai', function (Blueprint $table) {
            if (Schema::hasColumn('pkl_softnilai', 'pkl_id')) {
                // Drop FK first (assuming name pkl_softnilai_pkl_id_foreign or similar)
                // We use array syntax which handles standard naming
                $table->dropForeign(['pkl_id']);
                $table->dropColumn('pkl_id');
            }
            if (!Schema::hasColumn('pkl_softnilai', 'dudi_id')) {
                $table->foreignId('dudi_id')->after('student_id')->constrained('dudis')->onDelete('cascade');
            }
        });

        // Update pkl_hardnilai
        Schema::table('pkl_hardnilai', function (Blueprint $table) {
            if (Schema::hasColumn('pkl_hardnilai', 'pkl_id')) {
                $table->dropForeign(['pkl_id']);
                $table->dropColumn('pkl_id');
            }
            if (!Schema::hasColumn('pkl_hardnilai', 'dudi_id')) {
                $table->foreignId('dudi_id')->after('student_id')->constrained('dudis')->onDelete('cascade');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_softnilai', function (Blueprint $table) {
            $table->dropForeign(['dudi_id']);
            $table->dropColumn('dudi_id');
            $table->foreignId('pkl_id')->nullable()->constrained('pkls')->onDelete('cascade');
        });

        Schema::table('pkl_hardnilai', function (Blueprint $table) {
            $table->dropForeign(['dudi_id']);
            $table->dropColumn('dudi_id');
            $table->foreignId('pkl_id')->nullable()->constrained('pkls')->onDelete('cascade');
        });
    }
};
