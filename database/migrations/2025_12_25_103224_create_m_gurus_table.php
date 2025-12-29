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
        Schema::create('m_gurus', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('nip')->nullable();
            $table->string('nuptk')->nullable();
            $table->string('nama');
            $table->boolean('is_active')->default(true);
            $table->boolean('is_piket')->default(false);
            $table->boolean('is_bk')->default(false);
            $table->boolean('is_walas')->default(false);
            $table->boolean('is_kepsek')->default(false);
            $table->foreignId('user_id')->nullable()->constrained('users')->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('m_gurus');
    }
};
