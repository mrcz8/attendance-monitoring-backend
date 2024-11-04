<?php

namespace App\Import;

use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use PhpOffice\PhpSpreadsheet\IOFactory;

class Excel
{
    public function attendanceLog(object $file)
    {
        $spreadsheet = IOFactory::load($file->getRealPath());

        $employeeData = [];

        $sheet1 = $spreadsheet->getSheet(0);
        $highestColumnIndex = $sheet1->getHighestDataColumn();

        $highestColumn = Coordinate::columnIndexFromString($highestColumnIndex);
        $this->extractEmployeeData($sheet1, $employeeData, [1, 2, 3], range(1, $highestColumn - 3));

        // Process Sheet 2 (Statistical Report of Attendance)
        $sheet2 = $spreadsheet->getSheet(1);
        $this->extractEmployeeData($sheet2, $employeeData, [1, 2, 3], []);

        // Process Sheet 3 (Attendance Record Report)
        $sheet3 = $spreadsheet->getSheet(2);

        // Process Sheet 4 (Exception Statistic Report)
        $sheet4 = $spreadsheet->getSheet(3);
        $this->extractExceptionData($sheet4, $employeeData);

        // Return the merged employee data
        return $employeeData;
    }

    private function extractEmployeeData($sheet, &$employeeData, $infoColumns, $attendanceColumns)
    {
        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            if (!empty($rowData[$infoColumns[0] - 1]) && !empty($rowData[$infoColumns[1] - 1])) {
                $id = $rowData[$infoColumns[0] - 1];
                $name = $rowData[$infoColumns[1] - 1];
                $department = $rowData[$infoColumns[2] - 1];

                if (!isset($employeeData[$id])) {
                    $employeeData[$id] = [
                        'id' => $id,
                        'name' => $name,
                        'department' => $department,
                    ];
                }
            }
        }
    }

    private function extractExceptionData($sheet, &$employeeData)
    {
        foreach ($sheet->getRowIterator(2) as $row) {
            $cellIterator = $row->getCellIterator();
            $cellIterator->setIterateOnlyExistingCells(false);

            $rowData = [];
            foreach ($cellIterator as $cell) {
                $rowData[] = $cell->getValue();
            }

            $id = $rowData[0] ?? null;
            $date = $rowData[3] ?? null;
            $timeIn = trim($rowData[4] ?? null);
            $timeOut = trim($rowData[5] ?? null);

            if (!empty($id) && isset($employeeData[$id])) {
                $employeeData[$id]['attendance_logs'][] = [
                    'date' => $date,
                    'time_in' => $timeIn,
                    'time_out' => $timeOut,
                ];
            }
        }
    }

    public function shiftSetting(object $file)
    {
        $spreadsheet = IOFactory::load($file->getRealPath());
        $shiftSheet = $spreadsheet->getSheet(0);

        $highestRow = $shiftSheet->getHighestDataRow();
        $shifts = [
            'first_time_zone' => [],
            'second_time_zone' => [],
        ];

        $firstZoneShift = 1;
        $secondZoneShift = 1;

        for ($row = 3; $row <= $highestRow; $row++) {
            // First time zone data
            $onDuty = $shiftSheet->getCell("B$row")->getValue();
            $offDuty = $shiftSheet->getCell("C$row")->getValue();

            if ($onDuty && $offDuty && $onDuty !== 'On-duty' && $offDuty !== 'Off-duty') {
                $shifts['first_time_zone'][] = [
                    'name' => "First time zone (shift {$firstZoneShift})",
                    'time_in' => $onDuty,
                    'time_out' => $offDuty,
                ];
                $firstZoneShift++;
            }

            // Second time zone data
            $secondOnDuty = $shiftSheet->getCell("D$row")->getValue();
            $secondOffDuty = $shiftSheet->getCell("E$row")->getValue();

            if ($secondOnDuty && $secondOffDuty && $secondOnDuty !== 'On-duty' && $secondOffDuty !== 'Off-duty') {
                $shifts['second_time_zone'][] = [
                    'name' => "Second time zone (shift {$secondZoneShift})",
                    'time_in' => $secondOnDuty,
                    'time_out' => $secondOffDuty,
                ];
                $secondZoneShift++;
            }
        }

        return $shifts;
    }
}
