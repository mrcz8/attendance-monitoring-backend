<?php

namespace App\Services;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use App\Models\Employee;
use App\Models\Shift;
use Carbon\Carbon;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AttendanceSchedule
{
    protected $spreadsheet;

    public function __construct()
    {
        $this->spreadsheet = new Spreadsheet();
    }

    public function generateReport($user, $year, $month)
    {
        $this->setupAttendanceSettingSheet($user);
        $this->setupScheduleSettingSheet($user, $year, $month);

        $this->spreadsheet->setActiveSheetIndex(1);

        $fileName = "AttSetting.xls";
        $filePath = storage_path('app/public/' . $fileName);
        $writer = new Xls($this->spreadsheet);
        $writer->save($filePath);

        $response = new StreamedResponse(function() use ($filePath) {
            readfile($filePath);
            unlink($filePath);
        });

        $response->headers->set('Content-Type', 'application/vnd.ms-excel');
        $response->headers->set('Content-Disposition', "attachment;filename={$fileName}");
        $response->headers->set('Cache-Control', 'max-age=0');
        $response->headers->set('Pragma', 'public');

        return $response;
    }

    private function setupScheduleSettingSheet($user, $year, $month)
    {
        $sheet = $this->spreadsheet->createSheet();
        $sheet->setTitle("Schedule Setting");

        $sheet->mergeCells('A1:AI1');
        $sheet->setCellValue('A1', "Schedule Setting Report");
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(24)->setBold(true);

        $sheet->mergeCells('A2:D2');
        $sheet->setCellValue('A2', "Special shifts: 25-Ask for leave, 26-Out, Null-Holiday");
        $sheet->getStyle('A2')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A2')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A2')->getFont()->setName('Arial')->setSize(10);

        $sheet->mergeCells('E2:AI2');
        $sheet->setCellValue('E2', "");

        $sheet->mergeCells('A3:D3');
        $sheet->setCellValue('A3', "Schedule date");
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(16);
        $sheet->mergeCells('E3:AI3');
        $sheet->setCellValue('E3', Carbon::create($year, $month)->startOfMonth()->format('Y-m-d'));
        $sheet->getStyle('E3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_LEFT);
        $sheet->getStyle('E3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E3')->getFont()->setName('Arial')->setSize(10);

        $sheet->mergeCells('A4:A5');
        $sheet->setCellValue('A4', "ID");
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("A")->setWidth(85, 'px');

        $sheet->mergeCells('B4:B5');
        $sheet->setCellValue('B4', "Name");
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("B")->setWidth(77, 'px');

        $sheet->mergeCells('C4:C5');
        $sheet->setCellValue('C4', "Department");
        $sheet->getStyle('C4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("C")->setWidth(86, 'px');

        $sheet->mergeCells('D4:D5');
        $sheet->setCellValue('D4', "Card number");
        $sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("D")->setWidth(91, 'px');

        $startDate = Carbon::create($year, $month)->startOfMonth();
        $daysInMonth = $startDate->daysInMonth;
        $totalDaysInCalendar = 31;

        $currentDate = $startDate->copy();

        for ($i = 1; $i <= $totalDaysInCalendar; $i++) {
            $dayNumber = ($i > $daysInMonth) ? ($i - $daysInMonth) : $i;

            if ($i > $daysInMonth) {
                $currentDate = $currentDate->copy()->addDay();
            } else {
                $currentDate = $startDate->copy()->day($i);
            }

            $column = Coordinate::stringFromColumnIndex(4 + $i);

            // Set Day Number
            $sheet->setCellValue("{$column}4", $dayNumber);
            $sheet->getStyle("{$column}4")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$column}4")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$column}4")->getFont()->setName('Arial')->setSize(10);
            $sheet->getColumnDimension("{$column}")->setWidth(24, 'px');

            // Set Day Name
            $sheet->setCellValue("{$column}5", strtoupper($currentDate->format('D')));
            $sheet->getStyle("{$column}5")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("{$column}5")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("{$column}5")->getFont()->setName('Arial')->setSize(6);
        }

        $sheet->getStyle('A1:AI1005')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $employees = Employee::with('department', 'shift')->where('user_id', $user->id)->get();
        $row = 6;

        foreach ($employees as $employee) {
            $sheet->setCellValue("A{$row}", $employee->id);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setName('Arial')->setSize(10);

            $sheet->setCellValue("B{$row}", $employee->name);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$row}")->getFont()->setName('Arial')->setSize(10);

            $sheet->setCellValue("C{$row}", $employee->department->name ?? '');
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("C{$row}")->getFont()->setName('Arial')->setSize(10);

            $sheet->setCellValue("D{$row}", '');


            for ($day = 1; $day <= $totalDaysInCalendar; $day++) {
                $dateColumn = Coordinate::stringFromColumnIndex(4 + $day);

                $currentDate = ($day > $daysInMonth)
                    ? $startDate->copy()->addDays($day - 1)
                    : $startDate->copy()->day($day);

                $dayOfWeek = $currentDate->format('N');

                if ($dayOfWeek == 6 || $dayOfWeek == 7) {
                    $sheet->setCellValue("{$dateColumn}{$row}", '');
                } else {
                    $sheet->setCellValue("{$dateColumn}{$row}", $employee->shift_id ?? '');
                    $sheet->getStyle("{$dateColumn}{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                    $sheet->getStyle("{$dateColumn}{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
                    $sheet->getStyle("{$dateColumn}{$row}")->getFont()->setName('Arial')->setSize(10);
                }
            }

            $row++;
        }
    }

    private function setupAttendanceSettingSheet($user)
    {
        $sheet = $this->spreadsheet->getSheet(0);
        $sheet->setTitle("Att. Setting");

        $sheet->mergeCells('A1:G2');
        $sheet->setCellValue('A1', "Attendance Setting Report");
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A1')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A1')->getFont()->setName('Arial')->setSize(24)->setBold(true);

        $sheet->mergeCells('A3:G3');
        $sheet->setCellValue('A3', "Shift");
        $sheet->getStyle('A3')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A3')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A3')->getFont()->setName('Arial')->setSize(10);

        $sheet->mergeCells('A4:A5');
        $sheet->setCellValue('A4', "Number");
        $sheet->getStyle('A4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("A")->setWidth(49, 'px');

        $sheet->mergeCells('B4:C4');
        $sheet->setCellValue('B4', "First time zone");
        $sheet->getStyle('B4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("B")->setWidth(63, 'px');

        $sheet->mergeCells('D4:E4');
        $sheet->setCellValue('D4', "Second time zone");
        $sheet->getStyle('D4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("D")->setWidth(63, 'px');

        $sheet->mergeCells('F4:G4');
        $sheet->setCellValue('F4', "Overtime");
        $sheet->getStyle('F4')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F4')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F4')->getFont()->setName('Arial')->setSize(10);
        $sheet->getColumnDimension("F")->setWidth(63, 'px');

        $sheet->setCellValue('B5', "On-duty");
        $sheet->getStyle('B5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B5')->getFont()->setName('Arial')->setSize(10);

        $sheet->setCellValue('C5', "Off-duty");
        $sheet->getStyle('C5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C5')->getFont()->setName('Arial')->setSize(10);

        $sheet->setCellValue('D5', "On-duty");
        $sheet->getStyle('D5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D5')->getFont()->setName('Arial')->setSize(10);

        $sheet->setCellValue('E5', "Off-duty");
        $sheet->getStyle('E5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E5')->getFont()->setName('Arial')->setSize(10);

        $sheet->setCellValue('F5', "Check-In");
        $sheet->getStyle('F5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F5')->getFont()->setName('Arial')->setSize(10);

        $sheet->setCellValue('G5', "Check-Out");
        $sheet->getStyle('G5')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G5')->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
        $sheet->getStyle('G5')->getFont()->setName('Arial')->setSize(10);

        $sheet->getStyle('A1:G29')->getBorders()->getAllBorders()->setBorderStyle(Border::BORDER_THIN);

        $shifts = Shift::where('user_id', $user->id);
        $row = 6;
        foreach ($shifts as $index => $shift) {
            $sheet->setCellValue("A{$row}", $index + 1);
            $sheet->getStyle("A{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("A{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("A{$row}")->getFont()->setName('Arial')->setSize(10);

            $sheet->setCellValue("B{$row}", $shift->time_in);
            $sheet->getStyle("B{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("B{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("B{$row}")->getFont()->setName('Arial')->setSize(10);

            $sheet->setCellValue("C{$row}", $shift->time_out);
            $sheet->getStyle("C{$row}")->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle("C{$row}")->getAlignment()->setVertical(Alignment::VERTICAL_CENTER);
            $sheet->getStyle("C{$row}")->getFont()->setName('Arial')->setSize(10);

            $row++;
        }
    }
}