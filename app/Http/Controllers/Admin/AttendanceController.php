<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Display attendance list.
     */
    public function index(Request $request)
    {
        $query = Attendance::with('student')->orderBy('checktime', 'desc');

        // Filter by date
        $date = $request->get('date', now()->toDateString());
        if ($date) {
            $query->whereDate('checktime', $date);
        }

        // Filter by class
        if ($request->has('class') && $request->class) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('class', $request->class);
            });
        }

        // Filter by checktype
        if ($request->has('checktype') && $request->checktype !== '') {
            $query->where('checktype', $request->checktype);
        }

        // Search by NIS or name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhereHas('student', function ($sq) use ($search) {
                      $sq->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $attendances = $query->paginate(20)->withQueryString();

        // Get unique classes for filter
        $classes = Student::distinct()->pluck('class')->filter()->sort();

        // Statistics for selected date
        $totalStudents = Student::active()->count();
        $presentCount = Attendance::whereDate('checktime', $date)
            ->where('checktype', 0)
            ->distinct('nis')
            ->count('nis');
        $checkoutCount = Attendance::whereDate('checktime', $date)
            ->where('checktype', 1)
            ->distinct('nis')
            ->count('nis');

        return view('admin.attendance.index', compact(
            'attendances',
            'classes',
            'date',
            'totalStudents',
            'presentCount',
            'checkoutCount'
        ));
    }

    /**
     * Show attendance detail.
     */
    public function show(Attendance $attendance)
    {
        $attendance->load('student');
        return view('admin.attendance.show', compact('attendance'));
    }

    /**
     * Delete attendance record.
     */
    public function destroy(Attendance $attendance)
    {
        $attendance->delete();

        return redirect()->route('admin.attendance.index')
            ->with('success', 'Data absensi berhasil dihapus.');
    }

    /**
     * Export attendance to CSV.
     */
    public function export(Request $request)
    {
        $date = $request->get('date', now()->toDateString());
        
        $attendances = Attendance::with('student')
            ->whereDate('checktime', $date)
            ->orderBy('checktime', 'asc')
            ->get();

        $filename = "absensi_{$date}.csv";
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        $callback = function () use ($attendances) {
            $file = fopen('php://output', 'w');
            
            // Header
            fputcsv($file, ['No', 'NIS', 'Nama', 'Kelas', 'Waktu', 'Tipe']);
            
            // Data
            foreach ($attendances as $index => $attendance) {
                fputcsv($file, [
                    $index + 1,
                    $attendance->nis,
                    $attendance->student->name ?? '-',
                    $attendance->student->class ?? '-',
                    $attendance->checktime->format('Y-m-d H:i:s'),
                    $attendance->checktype == 0 ? 'Masuk' : 'Pulang',
                ]);
            }
            
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
