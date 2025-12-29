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
        Schema::create('m_users', function (Blueprint $table) {
            $table->id();
            $table->string('name', 100);
            $table->string('foto')->nullable();
            $table->string('username', 50)->unique();
            $table->string('password');
            $table->enum('level', ['admin', 'guru', 'siswa', 'pds']);
            $table->boolean('is_active')->nullable();
            $table->boolean('is_walas')->nullable();
            $table->boolean('is_gurupiker')->nullable();
            $table->boolean('is_kepsek')->nullable();
            $table->boolean('is_bk')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_users');
    }
};
