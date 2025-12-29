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
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['is_walas', 'is_gurupiker', 'is_kepsek', 'is_bk']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->boolean('is_walas')->nullable();
            $table->boolean('is_gurupiker')->nullable();
            $table->boolean('is_kepsek')->nullable();
            $table->boolean('is_bk')->nullable();
        });
    }
};
