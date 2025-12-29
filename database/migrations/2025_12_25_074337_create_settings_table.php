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
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->timestamps();
        });

        // Insert default settings
        $defaults = [
            ['key' => 'school_name', 'value' => 'SMK NEGERI 1 CONTOH'],
            ['key' => 'school_address', 'value' => 'Jl. Pendidikan No. 1, Kota Contoh'],
            ['key' => 'school_phone', 'value' => '(021) 1234567'],
            ['key' => 'school_email', 'value' => 'info@smkn1contoh.sch.id'],
            ['key' => 'school_website', 'value' => 'www.smkn1contoh.sch.id'],
            ['key' => 'principal_name', 'value' => 'Drs. Nama Kepala Sekolah, M.Pd'],
            ['key' => 'principal_nip', 'value' => '196501011990031001'],
            ['key' => 'school_logo', 'value' => null],
        ];

        foreach ($defaults as $setting) {
            \DB::table('settings')->insert([
                'key' => $setting['key'],
                'value' => $setting['value'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('settings');
    }
};
