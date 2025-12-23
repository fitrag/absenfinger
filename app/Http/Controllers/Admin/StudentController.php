<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentController extends Controller
{
    /**
     * Display student list.
     */
    public function index(Request $request)
    {
        $query = Student::query();

        // Filter by class
        if ($request->has('class') && $request->class) {
            $query->where('class', $request->class);
        }

        // Filter by status
        if ($request->has('status') && $request->status !== '') {
            $query->where('is_active', $request->status);
        }

        // Search by NIS or name
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('nis', 'like', "%{$search}%")
                  ->orWhere('name', 'like', "%{$search}%")
                  ->orWhere('finger_id', 'like', "%{$search}%");
            });
        }

        $students = $query->orderBy('class')->orderBy('name')
            ->paginate(20)->withQueryString();

        // Get unique classes for filter
        $classes = Student::distinct()->pluck('class')->filter()->sort();

        // Statistics
        $totalStudents = Student::count();
        $activeStudents = Student::where('is_active', true)->count();

        return view('admin.students.index', compact(
            'students',
            'classes',
            'totalStudents',
            'activeStudents'
        ));
    }

    /**
     * Show create form.
     */
    public function create()
    {
        $classes = Student::distinct()->pluck('class')->filter()->sort();
        $majors = Student::distinct()->pluck('major')->filter()->sort();
        
        return view('admin.students.create', compact('classes', 'majors'));
    }

    /**
     * Store new student.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'finger_id' => 'required|string|max:50|unique:students',
            'nis' => 'required|string|max:50|unique:students',
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:50',
            'major' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Student::create($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil ditambahkan.');
    }

    /**
     * Show student detail.
     */
    public function show(Student $student)
    {
        $student->load(['attendances' => function ($query) {
            $query->orderBy('checktime', 'desc')->limit(20);
        }]);

        return view('admin.students.show', compact('student'));
    }

    /**
     * Show edit form.
     */
    public function edit(Student $student)
    {
        $classes = Student::distinct()->pluck('class')->filter()->sort();
        $majors = Student::distinct()->pluck('major')->filter()->sort();

        return view('admin.students.edit', compact('student', 'classes', 'majors'));
    }

    /**
     * Update student.
     */
    public function update(Request $request, Student $student)
    {
        $validated = $request->validate([
            'finger_id' => 'required|string|max:50|unique:students,finger_id,' . $student->id,
            'nis' => 'required|string|max:50|unique:students,nis,' . $student->id,
            'name' => 'required|string|max:255',
            'class' => 'required|string|max:50',
            'major' => 'required|string|max:100',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $student->update($validated);

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil diperbarui.');
    }

    /**
     * Delete student.
     */
    public function destroy(Student $student)
    {
        $student->delete();

        return redirect()->route('admin.students.index')
            ->with('success', 'Data siswa berhasil dihapus.');
    }
}
