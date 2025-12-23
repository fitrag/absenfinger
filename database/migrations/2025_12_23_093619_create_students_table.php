<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('students', function (Blueprint $table) {
            $table->id();
            $table->string('finger_id')->unique()->comment('ID dari mesin fingerprint');
            $table->string('nis')->unique()->comment('Nomor Induk Siswa');
            $table->string('name');
            $table->string('class')->nullable();
            $table->string('major')->nullable()->comment('Jurusan');
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('students');
    }
};
