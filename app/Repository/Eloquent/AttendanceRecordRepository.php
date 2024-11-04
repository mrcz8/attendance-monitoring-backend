<?php

namespace App\Repository\Eloquent;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\User;
use App\Repository\AttendanceRecordRepositoryInterface;
use App\Repository\Base\BaseRepository;
use App\Utility\Paginate;
use Illuminate\Support\Facades\DB;

class AttendanceRecordRepository extends BaseRepository implements AttendanceRecordRepositoryInterface
{
    protected array $defaultFilters = [
        /**
         * Search keyword
         * This filters the items with a keyword. When this value is null, this filter is skipped.
         */
        'q' => null,

        /**
         * Pagination
         * The current page of items to get
         */
        'page' => 1,
    ];

    public function __construct(AttendanceRecord $attendanceRecord)
    {
        parent::__construct($attendanceRecord);
    }

    public function list(User $user, array $filters = [])
    {

    }

    public function store(Employee $employee, array $logs)
    {
        foreach ($logs as $log) {
            $employee->attendanceRecords()->create([
                'date' => $log['date'],
                'time_in' => $log['time_in'],
                'time_out' => $log['time_out'],
            ]);
        }
    }
}