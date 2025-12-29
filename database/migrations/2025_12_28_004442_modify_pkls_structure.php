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
        Schema::table('pkls', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['nama_perusahaan', 'alamat_perusahaan', 'telepon_perusahaan', 'pembimbing_perusahaan']);
        });

        Schema::table('pkls', function (Blueprint $table) {
            // Add dudi_id foreign key
            $table->unsignedBigInteger('dudi_id')->nullable()->after('student_id');
            $table->foreign('dudi_id')->references('id')->on('dudis')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkls', function (Blueprint $table) {
            $table->dropForeign(['dudi_id']);
            $table->dropColumn('dudi_id');
        });

        Schema::table('pkls', function (Blueprint $table) {
            $table->string('nama_perusahaan')->after('student_id');
            $table->text('alamat_perusahaan')->nullable()->after('nama_perusahaan');
            $table->string('telepon_perusahaan', 20)->nullable()->after('alamat_perusahaan');
            $table->string('pembimbing_perusahaan')->nullable()->after('telepon_perusahaan');
        });
    }
};
