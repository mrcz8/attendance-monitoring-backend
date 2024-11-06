<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'employee_id',
        'date',
        'time_in',
        'time_out',
    ];

    protected $hidden = [
        'created_at',
        'updated_at'
    ];

    protected $appends = [
        'isLate',
        'lateDuration',
        'isUnderTime',
        'undertimeDuration',
        'isAbsent'
    ];

    public function getIsLateAttribute()
    {
        $shiftStartTime = strtotime($this->employee->shift->time_in);
        $attendanceTimeIn = strtotime($this->time_in);

        return $attendanceTimeIn > $shiftStartTime;
    }

    public function getLateDurationAttribute()
    {
        if ($this->isLate && $this->time_in !== "00:00:00" && !empty($this->time_in)) {
            $shiftStartTime = strtotime($this->employee->shift->time_in);
            $attendanceTimeIn = strtotime($this->time_in);

            return round(($attendanceTimeIn - $shiftStartTime) / 60);
        }

        return 0;
    }

    public function getIsUnderTimeAttribute()
    {
        if (empty($this->time_out) || $this->time_out === "00:00:00") {
            return false;
        }

        $shiftEndTime = strtotime($this->employee->shift->time_out);
        $attendanceTimeOut = strtotime($this->time_out);

        return $attendanceTimeOut < $shiftEndTime;
    }

    public function getUndertimeDurationAttribute()
    {
        if ($this->IsUnderTime && !empty($this->time_out) && $this->time_out !== "00:00:00") {
            $shiftEndTime = strtotime($this->employee->shift->time_out);
            $attendanceTimeOut = strtotime($this->time_out);

            return round(($shiftEndTime - $attendanceTimeOut) / 60);
        }

        return 0;
    }

    public function getIsAbsentAttribute()
    {
        return empty($this->time_in) || $this->time_in === "00:00:00";
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
}
