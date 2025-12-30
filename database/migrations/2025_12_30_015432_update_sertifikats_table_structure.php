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
        Schema::table('sertifikats', function (Blueprint $table) {
            // Drop foreign keys first if they exist
            // Note: We need to know the exact constraint name or use array syntax for dropForeign
            // Usually 'table_column_foreign'
            $table->dropForeign(['pkl_id']);
            $table->dropForeign(['created_by']);

            // Drop columns
            $table->dropColumn([
                'pkl_id',
                'nomor_sertifikat',
                'tanggal_terbit',
                'nilai',
                'predikat',
                'file_sertifikat',
                'keterangan',
                'created_by'
            ]);

            // Add new columns
            $table->string('bgFront')->nullable()->after('id');
            $table->string('bgBack')->nullable()->after('bgFront');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('sertifikats', function (Blueprint $table) {
            // Reverse changes
            $table->dropColumn(['bgFront', 'bgBack']);

            // Add back columns (simplified for down)
            $table->foreignId('pkl_id')->constrained('pkls')->onDelete('cascade');
            $table->string('nomor_sertifikat', 100);
            $table->date('tanggal_terbit');
            $table->decimal('nilai', 5, 2)->nullable();
            $table->enum('predikat', ['Sangat Baik', 'Baik', 'Cukup', 'Kurang']);
            $table->string('file_sertifikat')->nullable();
            $table->text('keterangan')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->foreign('created_by')->references('id')->on('users')->onDelete('set null');
        });
    }
};
