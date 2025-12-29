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
            $table->unsignedBigInteger('tp_id')->nullable()->after('created_by');
            $table->foreign('tp_id')->references('id')->on('m_tp')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pkls', function (Blueprint $table) {
            $table->dropForeign(['tp_id']);
            $table->dropColumn('tp_id');
        });
    }
};
