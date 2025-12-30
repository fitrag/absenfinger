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
        Schema::create('pkl_komphards', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('m_jurusan_id');
            $table->string('nama');
            $table->timestamps();

            $table->foreign('m_jurusan_id')->references('id')->on('m_jurusan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_komphards');
    }
};
