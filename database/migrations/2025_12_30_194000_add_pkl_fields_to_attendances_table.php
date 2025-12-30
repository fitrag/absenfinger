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
        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('dudi_id')->nullable()->after('checktype')->constrained('dudis')->nullOnDelete();
            $table->decimal('latitude', 10, 8)->nullable()->after('dudi_id');
            $table->decimal('longitude', 11, 8)->nullable()->after('latitude');
            $table->boolean('is_pkl')->default(false)->after('longitude')->comment('True jika ini absensi PKL');
            
            $table->index(['nis', 'dudi_id', 'checktime']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['dudi_id']);
            $table->dropIndex(['nis', 'dudi_id', 'checktime']);
            $table->dropColumn(['dudi_id', 'latitude', 'longitude', 'is_pkl']);
        });
    }
};
