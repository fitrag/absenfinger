<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Attendance;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;

class AttendanceController extends Controller
{
    /**
     * Menerima data absensi dari server lokal fingerprint.
     * 
     * Endpoint: POST /api/attendance/sync
     * 
     * Format request yang diterima:
     * {
     *     "nis": "2024001",
     *     "checktime": "2024-12-23 08:00:00",
     *     "checktype": 0
     * }
     * 
     * checktype: 0 = check in, 1 = check out
     * 
     * Atau format batch:
     * {
     *     "data": [
     *         {"nis": "2024001", "checktime": "2024-12-23 08:00:00", "checktype": 0},
     *         {"nis": "2024002", "checktime": "2024-12-23 08:01:00", "checktype": 0}
     *     ]
     * }
     */
    public function sync(Request $request): JsonResponse
    {
        try {
            // Check if batch or single record
            if ($request->has('data') && is_array($request->data)) {
                return $this->syncBatch($request->data);
            }

            return $this->syncSingle($request);
        } catch (\Exception $e) {
            Log::error('Attendance sync error: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString(),
                'request' => $request->all()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan data absensi',
                'error' => config('app.debug') ? $e->getMessage() : 'Internal server error'
            ], 500);
        }
    }

    /**
     * Sync single attendance record.
     */
    protected function syncSingle(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nis' => 'required|string|max:50',
            'checktime' => 'required|date',
            'checktype' => 'required|in:0,1',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        $result = $this->processAttendance($validator->validated());

        if ($result['success']) {
            $response = [
                'success' => true,
                'message' => $result['message'],
            ];

            if (isset($result['attendance'])) {
                $response['data'] = $result['attendance'];
            }

            if ($result['skipped'] ?? false) {
                $response['skipped'] = true;
                return response()->json($response, 200);
            }

            return response()->json($response, 201);
        }

        return response()->json([
            'success' => false,
            'message' => $result['message']
        ], $result['code'] ?? 400);
    }

    /**
     * Sync batch attendance records.
     */
    protected function syncBatch(array $data): JsonResponse
    {
        $results = [
            'success' => 0,
            'failed' => 0,
            'skipped' => 0,
            'errors' => []
        ];

        DB::beginTransaction();

        try {
            foreach ($data as $index => $record) {
                $validator = Validator::make($record, [
                    'nis' => 'required|string|max:50',
                    'checktime' => 'required|date',
                    'checktype' => 'required|in:0,1',
                ]);

                if ($validator->fails()) {
                    $results['failed']++;
                    $results['errors'][] = [
                        'index' => $index,
                        'nis' => $record['nis'] ?? 'unknown',
                        'errors' => $validator->errors()->toArray()
                    ];
                    continue;
                }

                $result = $this->processAttendance($validator->validated());

                if ($result['success']) {
                    if ($result['skipped'] ?? false) {
                        $results['skipped']++;
                    } else {
                        $results['success']++;
                    }
                } else {
                    $results['failed']++;
                    $results['errors'][] = [
                        'index' => $index,
                        'nis' => $record['nis'],
                        'message' => $result['message']
                    ];
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Batch sync selesai: {$results['success']} berhasil, {$results['skipped']} dilewati, {$results['failed']} gagal",
                'results' => $results
            ], 200);

        } catch (\Exception $e) {
            DB::rollBack();
            throw $e;
        }
    }

    /**
     * Process single attendance record.
     */
    protected function processAttendance(array $data): array
    {
        $checktime = Carbon::parse($data['checktime']);

        // Check for duplicate (same nis, same time within 1 minute)
        $duplicate = Attendance::where('nis', $data['nis'])
            ->whereBetween('checktime', [
                $checktime->copy()->subMinute(),
                $checktime->copy()->addMinute()
            ])
            ->exists();

        if ($duplicate) {
            return [
                'success' => true,
                'skipped' => true,
                'message' => 'Data sudah ada (duplikat)'
            ];
        }

        // Create attendance record
        $attendance = Attendance::create([
            'nis' => $data['nis'],
            'checktime' => $checktime,
            'checktype' => (int) $data['checktype'],
        ]);

        return [
            'success' => true,
            'message' => 'Data absensi berhasil disimpan',
            'attendance' => $attendance
        ];
    }

    /**
     * Get attendance records with optional filters.
     * 
     * Endpoint: GET /api/attendance
     */
    public function index(Request $request): JsonResponse
    {
        $query = Attendance::with('student:id,name,nis,class');

        // Filter by date
        if ($request->has('date')) {
            $query->whereDate('checktime', $request->date);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('checktime', [$request->start_date, $request->end_date]);
        }

        // Filter by nis
        if ($request->has('nis')) {
            $query->where('nis', $request->nis);
        }

        // Filter by checktype
        if ($request->has('checktype')) {
            $query->where('checktype', $request->checktype);
        }

        $attendances = $query->orderBy('checktime', 'desc')
            ->paginate($request->get('per_page', 50));

        return response()->json([
            'success' => true,
            'data' => $attendances
        ]);
    }

    /**
     * Get today's attendance summary.
     * 
     * Endpoint: GET /api/attendance/today
     */
    public function today(): JsonResponse
    {
        $today = now()->toDateString();

        $summary = [
            'date' => $today,
            'total_students' => Student::active()->count(),
            'check_in' => Attendance::whereDate('checktime', $today)
                ->where('checktype', 0)
                ->distinct('nis')
                ->count('nis'),
            'check_out' => Attendance::whereDate('checktime', $today)
                ->where('checktype', 1)
                ->distinct('nis')
                ->count('nis'),
        ];

        $summary['absent'] = $summary['total_students'] - $summary['check_in'];

        $recentAttendances = Attendance::with('student:id,name,nis,class')
            ->whereDate('checktime', $today)
            ->orderBy('checktime', 'desc')
            ->limit(20)
            ->get();

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'recent' => $recentAttendances
        ]);
    }

    /**
     * Get students with absence status (sakit/izin).
     * 
     * Endpoint: GET /api/attendance/absences
     * 
     * Query Parameters:
     * - date: Filter by specific date (Y-m-d format)
     * - start_date: Start date for range filter
     * - end_date: End date for range filter
     * - status: Filter by status (sakit, izin, or all for both)
     * - kelas_id: Filter by class ID
     * - per_page: Number of records per page (default: 50)
     */
    public function absences(Request $request): JsonResponse
    {
        $query = Attendance::with(['student.kelas', 'student.jurusan'])
            ->whereIn('checktype', [
                Attendance::TYPE_SAKIT,
                Attendance::TYPE_IZIN,
            ]);

        // Filter by specific date
        if ($request->has('date')) {
            $query->whereDate('checktime', $request->date);
        }

        // Filter by date range
        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('checktime', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        // Default to today if no date filter provided
        if (!$request->has('date') && !$request->has('start_date')) {
            $query->whereDate('checktime', now()->toDateString());
        }

        // Filter by specific status
        if ($request->has('status')) {
            if ($request->status === 'sakit') {
                $query->where('checktype', Attendance::TYPE_SAKIT);
            } elseif ($request->status === 'izin') {
                $query->where('checktype', Attendance::TYPE_IZIN);
            }
        }

        // Filter by kelas ID
        if ($request->has('kelas_id')) {
            $query->whereHas('student', function ($q) use ($request) {
                $q->where('kelas_id', $request->kelas_id);
            });
        }

        $absences = $query->orderBy('checktime', 'desc')->get();

        // Transform response data
        $data = $absences->map(function ($absence) {
            return [
                'id' => $absence->id,
                'nama' => $absence->student->name ?? null,
                'no_tlp' => $absence->student->no_tlp ?? null,
                'keterangan' => $absence->checktype_label,
            ];
        });

        // Get summary counts
        $dateFilter = $request->date ?? now()->toDateString();
        $summary = [
            'date' => $dateFilter,
            'sakit' => Attendance::whereDate('checktime', $dateFilter)
                ->where('checktype', Attendance::TYPE_SAKIT)
                ->count(),
            'izin' => Attendance::whereDate('checktime', $dateFilter)
                ->where('checktype', Attendance::TYPE_IZIN)
                ->count(),
        ];
        $summary['total'] = $summary['sakit'] + $summary['izin'];

        return response()->json([
            'success' => true,
            'summary' => $summary,
            'data' => $data,
        ]);
    }
}
