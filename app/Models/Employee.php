<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'department_id',
        'shift_id',
        'biometric_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    protected $appends = [
        'totalLateDurationForMonth'
    ];

    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function shift()
    {
        return $this->belongsTo(Shift::class);
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getTotalLateDurationForMonthAttribute()
    {
        $year = request()->query('year', now()->year);
        $month = request()->query('month', now()->month);

        $attendanceRecords = $this->attendanceRecords()
            ->whereYear('date', $year)
            ->whereMonth('date', $month)
            ->get();

        return $attendanceRecords->sum(function ($record) {
            return $record->lateDuration;
        });
    }
}
