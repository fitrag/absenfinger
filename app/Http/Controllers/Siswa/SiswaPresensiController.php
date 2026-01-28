<?php

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class SiswaPresensiController extends Controller
{
    /**
     * Get the logged-in student.
     */
    private function getStudent()
    {
        $userId = Session::get('user_id');

        return Student::with('kelas')
            ->where('user_id', $userId)
            ->first();
    }

    /**
     * Display presensi list for the logged-in student.
     */
    public function index(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Default date range: current month
        $startDate = $request->input('start_date', Carbon::now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get attendance records for this student within date range
        $attendances = Attendance::where('nis', $student->nis)
            ->whereDate('checktime', '>=', $startDate)
            ->whereDate('checktime', '<=', $endDate)
            ->where('is_pkl', false) // Only regular attendance
            ->orderBy('checktime', 'desc')
            ->get();

        // Calculate stats
        $totalHadir = $attendances->where('checktype', Attendance::TYPE_MASUK)->count();
        $totalSakit = $attendances->where('checktype', Attendance::TYPE_SAKIT)->count();
        $totalIzin = $attendances->where('checktype', Attendance::TYPE_IZIN)->count();
        $totalAlpha = $attendances->where('checktype', Attendance::TYPE_ALPHA)->count();

        // Group by date
        $groupedAttendances = $attendances->groupBy(function ($item) {
            return $item->checktime->format('Y-m-d');
        });

        return view('siswa.presensi.index', compact(
            'student',
            'attendances',
            'groupedAttendances',
            'startDate',
            'endDate',
            'totalHadir',
            'totalSakit',
            'totalIzin',
            'totalAlpha'
        ));
    }

    /**
     * Display presensi chart for the logged-in student.
     */
    public function grafik(Request $request)
    {
        $student = $this->getStudent();

        if (!$student) {
            abort(403, 'Data siswa tidak ditemukan');
        }

        // Default date range: last 14 days
        $startDate = $request->input('start_date', Carbon::now()->subDays(13)->format('Y-m-d'));
        $endDate = $request->input('end_date', Carbon::now()->format('Y-m-d'));

        // Get attendance records for this student within date range
        $attendances = Attendance::where('nis', $student->nis)
            ->whereDate('checktime', '>=', $startDate)
            ->whereDate('checktime', '<=', $endDate)
            ->where('is_pkl', false)
            ->whereIn('checktype', [Attendance::TYPE_MASUK, Attendance::TYPE_PULANG])
            ->orderBy('checktime', 'asc')
            ->get();

        // Group by date and separate masuk/pulang
        $chartData = [];
        $dates = [];

        // Generate all dates in range
        $current = Carbon::parse($startDate);
        $end = Carbon::parse($endDate);
        while ($current <= $end) {
            $dateStr = $current->format('Y-m-d');
            $dates[] = $dateStr;
            $chartData[$dateStr] = [
                'date' => $current->format('d M'),
                'day' => $current->translatedFormat('D'),
                'masuk' => null,
                'pulang' => null,
                'masuk_time' => null,
                'pulang_time' => null,
            ];
            $current->addDay();
        }

        // Fill in actual attendance data
        foreach ($attendances as $attendance) {
            $dateStr = $attendance->checktime->format('Y-m-d');
            if (isset($chartData[$dateStr])) {
                $timeInMinutes = $attendance->checktime->hour * 60 + $attendance->checktime->minute;
                if ($attendance->checktype === Attendance::TYPE_MASUK) {
                    $chartData[$dateStr]['masuk'] = $timeInMinutes;
                    $chartData[$dateStr]['masuk_time'] = $attendance->checktime->format('H:i');
                } elseif ($attendance->checktype === Attendance::TYPE_PULANG) {
                    $chartData[$dateStr]['pulang'] = $timeInMinutes;
                    $chartData[$dateStr]['pulang_time'] = $attendance->checktime->format('H:i');
                }
            }
        }

        // Convert to array for view
        $chartDataArray = array_values($chartData);

        return view('siswa.presensi.grafik', compact(
            'student',
            'startDate',
            'endDate',
            'chartDataArray'
        ));
    }
}
