<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Insert new settings for system name and letterhead (kop surat)
        $newSettings = [
            ['key' => 'system_name', 'value' => 'AbsenFinger'],
            ['key' => 'letterhead', 'value' => 'PEMERINTAH KABUPATEN/KOTA'],
            ['key' => 'letterhead_sub', 'value' => 'DINAS PENDIDIKAN DAN KEBUDAYAAN'],
        ];

        foreach ($newSettings as $setting) {
            // Only insert if not exists
            $exists = DB::table('settings')->where('key', $setting['key'])->exists();
            if (!$exists) {
                DB::table('settings')->insert([
                    'key' => $setting['key'],
                    'value' => $setting['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::table('settings')->whereIn('key', ['system_name', 'letterhead', 'letterhead_sub'])->delete();
    }
};
