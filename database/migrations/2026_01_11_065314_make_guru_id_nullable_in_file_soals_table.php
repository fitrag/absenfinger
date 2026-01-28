<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('file_soals', function (Blueprint $table) {
            $table->unsignedBigInteger('guru_id')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('file_soals', function (Blueprint $table) {
            $table->unsignedBigInteger('guru_id')->nullable(false)->change();
        });
    }
};
