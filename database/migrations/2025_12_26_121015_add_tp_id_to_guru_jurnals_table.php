<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guru_jurnals', function (Blueprint $table) {
            $table->foreignId('tp_id')->nullable()->after('guru_id')->constrained('m_tp')->onDelete('set null');
        });
    }

    public function down(): void
    {
        Schema::table('guru_jurnals', function (Blueprint $table) {
            $table->dropForeign(['tp_id']);
            $table->dropColumn('tp_id');
        });
    }
};
