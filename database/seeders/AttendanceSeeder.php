<?php

namespace Database\Seeders;

use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class AttendanceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all active students
        $students = Student::where('is_active', true)->get();

        if ($students->isEmpty()) {
            $this->command->info('No active students found. Please seed students first.');
            return;
        }

        // Generate attendance for the last 7 days
        $startDate = Carbon::now()->subDays(6);
        $endDate = Carbon::now();

        // Jam masuk normal: 06:30 - 07:00
        // Jam terlambat: 07:01 - 08:00
        // Jam pulang: 14:00 - 15:00

        $this->command->info('Generating attendance data...');

        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            // Skip weekend
            if ($date->isWeekend()) {
                continue;
            }

            foreach ($students as $student) {
                $random = rand(1, 100);

                // 10% chance: Sakit (tidak ada record sama sekali)
                if ($random <= 10) {
                    continue; // Skip - siswa sakit, tidak ada presensi
                }

                // 20% chance: Terlambat (07:01 - 08:00)
                if ($random <= 30) {
                    $checkInTime = $date->copy()->setTime(7, rand(1, 59), rand(0, 59));
                } else {
                    // 70% chance: Tepat waktu (06:30 - 07:00)
                    $checkInTime = $date->copy()->setTime(6, rand(30, 59), rand(0, 59));
                }

                // Create check-in record
                Attendance::create([
                    'nis' => $student->nis,
                    'checktime' => $checkInTime,
                    'checktype' => 0, // Masuk
                ]);

                // 85% chance: Siswa pulang dengan record
                if (rand(1, 100) <= 85) {
                    $checkOutTime = $date->copy()->setTime(rand(14, 15), rand(0, 59), rand(0, 59));

                    Attendance::create([
                        'nis' => $student->nis,
                        'checktime' => $checkOutTime,
                        'checktype' => 1, // Pulang
                    ]);
                }
            }

            $this->command->info('Generated attendance for: ' . $date->format('Y-m-d'));
        }

        $totalRecords = Attendance::count();
        $this->command->info("Total attendance records created: {$totalRecords}");
    }
}
