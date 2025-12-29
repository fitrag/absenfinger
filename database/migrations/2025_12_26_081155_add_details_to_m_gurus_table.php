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
        Schema::table('m_gurus', function (Blueprint $table) {
            $table->string('tmpt_lhr')->nullable()->after('nama');
            $table->date('tgl_lhr')->nullable()->after('tmpt_lhr');
            $table->enum('jen_kel', ['L', 'P'])->nullable()->after('tgl_lhr');
            $table->string('no_tlp', 20)->nullable()->after('jen_kel');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_gurus', function (Blueprint $table) {
            $table->dropColumn(['tmpt_lhr', 'tgl_lhr', 'jen_kel', 'no_tlp']);
        });
    }
};
