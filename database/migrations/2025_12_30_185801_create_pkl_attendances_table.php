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
        Schema::create('pkl_attendances', function (Blueprint $table) {
            $table->id();
            $table->string('nis')->comment('Nomor Induk Siswa');
            $table->foreignId('dudi_id')->constrained('dudis')->onDelete('cascade');
            $table->datetime('checktime')->comment('Waktu absensi');
            $table->tinyInteger('checktype')->default(0)->comment('0 = masuk, 1 = pulang');
            $table->decimal('latitude', 10, 8)->nullable()->comment('Latitude saat absensi');
            $table->decimal('longitude', 11, 8)->nullable()->comment('Longitude saat absensi');
            $table->timestamps();

            $table->index(['nis', 'checktime']);
            $table->index(['dudi_id', 'checktime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_attendances');
    }
};
