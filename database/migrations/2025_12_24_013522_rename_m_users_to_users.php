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
        // Add new columns to existing users table
        Schema::table('users', function (Blueprint $table) {
            $table->string('foto')->nullable()->after('email');
            $table->string('username', 50)->unique()->nullable()->after('name');
            $table->enum('level', ['admin', 'guru', 'siswa', 'pds'])->default('siswa')->after('password');
            $table->boolean('is_active')->nullable()->after('level');
            $table->boolean('is_walas')->nullable()->after('is_active');
            $table->boolean('is_gurupiker')->nullable()->after('is_walas');
            $table->boolean('is_kepsek')->nullable()->after('is_gurupiker');
            $table->boolean('is_bk')->nullable()->after('is_kepsek');
        });

        // Drop m_users table
        Schema::dropIfExists('m_users');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['foto', 'username', 'level', 'is_active', 'is_walas', 'is_gurupiker', 'is_kepsek', 'is_bk']);
        });
    }
};
