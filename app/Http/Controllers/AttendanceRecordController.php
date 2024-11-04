<?php

namespace App\Http\Controllers;

use App\Import\Excel;
use App\Models\AttendanceRecord;
use App\Models\Shift;
use App\Models\User;
use App\Modules\Message\Message;
use App\Services\AttendanceRecordServiceInterface;
use App\Services\ShiftServiceInterface;
use Illuminate\Http\Request;

class AttendanceRecordController extends Controller
{
    protected $attendanceService;

    protected $shiftService;

    public function __construct(AttendanceRecordServiceInterface $attendanceRecordServiceInterface, ShiftServiceInterface $shiftServiceInterface)
    {
        $this->attendanceService = $attendanceRecordServiceInterface;
        $this->shiftService = $shiftServiceInterface;
    }

    public function index(Request $request, Message $message)
    {
        $user = $request->user();
        $query = $request->query('q', null);
        $month = $request->query('month', null);
        $year = $request->query('year', null);
        $page = $request->query('page', null);
        $page = 1;

        $filters = [
            'q' => $query,
            'month' => $month,
            'year' => $year,
            'page' => $page,
        ];

        $paginatedItems = $this->attendanceService->list($user, $filters);
        $message->setContent(200, 'Attendance logs retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function importAttendanceLogs(Request $request, Message $message)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        // $user = $request->user();
        $user = User::findOrFail(2);

        $import = new Excel;
        $data = $import->attendanceLog($file);

        $result = $this->attendanceService->store($user, $data);

        if (is_array($result)) {
            $message->setContent(201, 'File uploaded and attendance records stored successfully.');
        } else {
            $message->setContent(400, 'Failed to store attendance records. Please check the file.');
        }

        return $message->render();
    }

    public function importShiftSettings(Request $request, Message $message)
    {
        $request->validate([
            'file' => 'required|mimes:xlsx,xls',
        ]);

        $file = $request->file('file');
        // $user = $request->user();
        $user = User::findOrFail(2);

        $import = new Excel;
        $shifts = $import->shiftSetting($file);

        if(!empty($shifts['first_time_zone'])) {
            $firstTimeZones = $shifts['first_time_zone'];
            foreach($firstTimeZones as $shift) {
                $result = $this->shiftService->store($user, $shift['name'], $shift['time_in'], $shift['time_out']);
            }
        }

        if(!empty($shifts['second_time_zone'])) {
            $secondTimeZones = $shifts['second_time_zone'];
            foreach($secondTimeZones as $shift) {
                $result = $this->shiftService->store($user, $shift['name'], $shift['time_in'], $shift['time_out']);
            }
        }

        if ($result instanceof Shift) {
            $message->setContent(201, 'File uploaded and shift settings stored successfully.');
        } else {
            $message->setContent(400, 'Failed to store shift setting records. Please check the file.');
        }

        return $message->render();
    }
}
