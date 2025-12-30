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
        Schema::create('pkl_nilai', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('pkl_kompsoft_id')->nullable();
            $table->unsignedBigInteger('pkl_komphard_id')->nullable();
            $table->decimal('nilai', 5, 2)->nullable();
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('pkl_kompsoft_id')->references('id')->on('pkl_kompsofts')->onDelete('cascade');
            $table->foreign('pkl_komphard_id')->references('id')->on('pkl_komphards')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_nilai');
    }
};
