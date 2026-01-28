<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Holiday extends Model
{
    protected $fillable = ['date', 'name', 'description', 'is_active'];

    protected $casts = [
        'date' => 'date',
        'is_active' => 'boolean',
    ];

    /**
     * Check if a specific date is a holiday.
     */
    public static function isHoliday($date): bool
    {
        $date = Carbon::parse($date)->format('Y-m-d');
        return self::where('date', $date)->where('is_active', true)->exists();
    }

    /**
     * Get all holidays between two dates.
     */
    public static function getHolidaysBetween($startDate, $endDate)
    {
        return self::where('is_active', true)
            ->whereBetween('date', [
                Carbon::parse($startDate)->format('Y-m-d'),
                Carbon::parse($endDate)->format('Y-m-d')
            ])
            ->orderBy('date')
            ->get();
    }

    /**
     * Get holidays as array of date strings.
     */
    public static function getHolidayDatesArray($startDate, $endDate): array
    {
        return self::getHolidaysBetween($startDate, $endDate)
            ->pluck('date')
            ->map(fn($date) => $date->format('Y-m-d'))
            ->toArray();
    }
}
