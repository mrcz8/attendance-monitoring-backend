<?php

namespace App\Http\Controllers;

use App\Modules\Message\Message;
use App\Services\AttendanceSchedule;
use Illuminate\Http\Request;
use PhpOffice\PhpSpreadsheet\Writer\Xls;

class GenerateExcelController extends Controller
{
    public function generate(Request $request, Message $message)
    {
        $user = $request->user();
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $generate = new AttendanceSchedule();
        $excel = $generate->generateReport($user, $year, $month);

        return $excel;
    }
}
