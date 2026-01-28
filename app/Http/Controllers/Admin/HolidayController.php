<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Holiday;
use Illuminate\Http\Request;
use Carbon\Carbon;

class HolidayController extends Controller
{
    /**
     * Display a listing of holidays.
     */
    public function index(Request $request)
    {
        $year = $request->get('year', now()->year);

        $holidays = Holiday::whereYear('date', $year)
            ->orderBy('date')
            ->paginate(50);

        $years = Holiday::selectRaw('YEAR(date) as year')
            ->distinct()
            ->orderBy('year', 'desc')
            ->pluck('year')
            ->toArray();

        // Add current year if not in list
        if (!in_array($year, $years)) {
            array_unshift($years, $year);
        }

        return view('admin.holidays.index', compact('holidays', 'year', 'years'));
    }

    /**
     * Store a newly created holiday.
     */
    public function store(Request $request)
    {
        $request->validate([
            'date' => 'required|date|unique:holidays,date',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        Holiday::create([
            'date' => $request->date,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => true,
        ]);

        return redirect()->back()->with('success', 'Hari libur berhasil ditambahkan');
    }

    /**
     * Update the specified holiday.
     */
    public function update(Request $request, $id)
    {
        $holiday = Holiday::findOrFail($id);

        $request->validate([
            'date' => 'required|date|unique:holidays,date,' . $id,
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $holiday->update([
            'date' => $request->date,
            'name' => $request->name,
            'description' => $request->description,
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->back()->with('success', 'Hari libur berhasil diperbarui');
    }

    /**
     * Remove the specified holiday.
     */
    public function destroy($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->delete();

        return redirect()->back()->with('success', 'Hari libur berhasil dihapus');
    }

    /**
     * Toggle holiday active status.
     */
    public function toggle($id)
    {
        $holiday = Holiday::findOrFail($id);
        $holiday->update(['is_active' => !$holiday->is_active]);

        return redirect()->back()->with('success', 'Status hari libur berhasil diubah');
    }
}
