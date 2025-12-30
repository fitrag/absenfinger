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
        Schema::create('pkl_komponennlai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('jurusan_id');
            $table->text('komp_sof')->nullable();
            $table->text('komp_hard')->nullable();
            $table->timestamps();

            $table->foreign('jurusan_id')->references('id')->on('m_jurusan')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_komponennlai');
    }
};
