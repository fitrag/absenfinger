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
        Schema::table('pkl_softnilai', function (Blueprint $table) {
            $table->unsignedBigInteger('m_tp_id')->nullable()->after('dudi_id');
            $table->foreign('m_tp_id')->references('id')->on('m_tp')->onDelete('set null');
        });

        Schema::table('pkl_hardnilai', function (Blueprint $table) {
            $table->unsignedBigInteger('m_tp_id')->nullable()->after('dudi_id');
            $table->foreign('m_tp_id')->references('id')->on('m_tp')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkl_softnilai', function (Blueprint $table) {
            $table->dropForeign(['m_tp_id']);
            $table->dropColumn('m_tp_id');
        });

        Schema::table('pkl_hardnilai', function (Blueprint $table) {
            $table->dropForeign(['m_tp_id']);
            $table->dropColumn('m_tp_id');
        });
    }
};

