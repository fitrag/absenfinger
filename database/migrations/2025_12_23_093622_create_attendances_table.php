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
        Schema::create('attendances', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->comment('Nomor Induk Siswa');
            $table->datetime('checktime')->comment('Waktu scan dari mesin');
            $table->tinyInteger('checktype')->default(0)->comment('0 = check in, 1 = check out');
            $table->timestamps();
            
            $table->index(['nis', 'checktime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('attendances');
    }
};
