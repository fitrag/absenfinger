<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('guru_jurnals', function (Blueprint $table) {
            $table->string('semester')->nullable()->after('tp_id'); // Semester (Ganjil/Genap)
        });
    }

    public function down(): void
    {
        Schema::table('guru_jurnals', function (Blueprint $table) {
            $table->dropColumn('semester');
        });
    }
};
