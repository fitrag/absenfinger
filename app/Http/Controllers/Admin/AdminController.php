<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        $today = now()->toDateString();

        // Statistics
        $totalStudents = Student::active()->count();
        $presentToday = Attendance::whereDate('checktime', $today)
            ->where('checktype', 0)
            ->distinct('nis')
            ->count('nis');
        $checkOutToday = Attendance::whereDate('checktime', $today)
            ->where('checktype', 1)
            ->distinct('nis')
            ->count('nis');
        $absentToday = $totalStudents - $presentToday;

        // Recent attendances
        $recentAttendances = Attendance::with('student')
            ->whereDate('checktime', $today)
            ->orderBy('checktime', 'desc')
            ->limit(10)
            ->get();

        // Weekly statistics for chart
        $weeklyData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dayName = $date->locale('id')->isoFormat('ddd');
            
            $present = Attendance::whereDate('checktime', $date->toDateString())
                ->where('checktype', 0)
                ->distinct('nis')
                ->count('nis');
            
            $weeklyData[] = [
                'day' => $dayName,
                'date' => $date->format('Y-m-d'),
                'present' => $present,
                'absent' => $totalStudents - $present,
            ];
        }

        return view('admin.dashboard', compact(
            'totalStudents',
            'presentToday',
            'checkOutToday',
            'absentToday',
            'recentAttendances',
            'weeklyData'
        ));
    }
}
