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
        Schema::create('pkl_wirausahanilais', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('student_id');
            $table->unsignedBigInteger('dudi_id')->nullable();
            $table->unsignedBigInteger('m_tp_id')->nullable();
            $table->unsignedBigInteger('pkl_kompwirausaha_id');
            $table->decimal('nilai', 5, 2)->default(0);
            $table->timestamps();

            $table->foreign('student_id')->references('id')->on('students')->onDelete('cascade');
            $table->foreign('dudi_id')->references('id')->on('dudis')->onDelete('set null');
            $table->foreign('m_tp_id')->references('id')->on('m_tp')->onDelete('set null');
            $table->foreign('pkl_kompwirausaha_id')->references('id')->on('pkl_kompwirausaha')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pkl_wirausahanilais');
    }
};
