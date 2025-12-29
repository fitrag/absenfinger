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
            $table->dropColumn(['is_active', 'is_piket', 'is_bk', 'is_walas', 'is_kepsek']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('m_gurus', function (Blueprint $table) {
            $table->boolean('is_active')->default(true);
            $table->boolean('is_piket')->default(false);
            $table->boolean('is_bk')->default(false);
            $table->boolean('is_walas')->default(false);
            $table->boolean('is_kepsek')->default(false);
        });
    }
};
