<?php

namespace App\Services;

use App\Models\Department;
use App\Models\Employee;
use App\Models\User;
use App\Repository\AttendanceRecordRepositoryInterface;
use App\Utility\Paginate;

class AttendanceRecordService implements AttendanceRecordServiceInterface
{
    protected $attendanceRecordRepository;

    protected $employeeService;

    protected $departmentService;

    public function __construct(
        AttendanceRecordRepositoryInterface $attendanceRecordRepositoryInterface,
        EmployeeServiceInterface $employeeServiceInterface,
        DepartmentServiceInterface $departmentServiceInterface)
    {
        $this->attendanceRecordRepository = $attendanceRecordRepositoryInterface;
        $this->employeeService = $employeeServiceInterface;
        $this->departmentService = $departmentServiceInterface;
    }

    public function list(User $user, array $filters = []): Paginate
    {
        return $this->employeeService->list($filters);
    }

    public function store(User $user, array $data): array
    {
        $employeeRecords = $this->filterEmployeeRecords($data);

        foreach ($employeeRecords as $record) {
            $this->processEmployeeRecord($user, $record);
        }

        return [];
    }

    private function filterEmployeeRecords(array $data): array
    {
        return array_filter($data, function ($record) {
            return isset($record['id']) && is_numeric($record['id']);
        });
    }

    private function processEmployeeRecord(User $user, array $record): void
    {
        $employee = $this->findEmployee($record['id']);

        if ($employee) {
            $this->handleExistingEmployee($user, $employee, $record);
        } else {
            $this->handleNewEmployee($user, $record);
        }
    }

    private function findEmployee(int $id): ?Employee
    {
        return $id ? $this->employeeService->find($id) : null;
    }

    private function handleExistingEmployee(User $user, Employee $employee, array $record): void
    {
        if (empty($employee->department_id)) {
            $this->addDepartmentToEmployee($user, $employee, $record);
        }

        $this->attendanceRecordRepository->store($employee, $record['attendance_logs']);
    }

    private function handleNewEmployee(User $user, array $record): void
    {
        $departmentName = $record['department'] ?? null;

        if ($departmentName) {
            $department = $this->getOrCreateDepartment($user, $departmentName);

            if ($department instanceof Department) {
                $newEmployee = $this->employeeService->store(
                    $user,
                    $record['name'],
                    null,
                    $department->id,
                    null
                );

                $this->attendanceRecordRepository->store($newEmployee, $record['attendance_logs']);
            }
        }
    }

    private function getOrCreateDepartment(User $user, string $departmentName): ?Department
    {
        $department = $this->departmentService->findDepartmentByName($user, $departmentName);

        if (!$department) {
            $department = $this->departmentService->store($user, $departmentName, []);
        }

        return $department;
    }

    private function addDepartmentToEmployee(User $user, Employee $employee, array $record): void
    {
        $departmentName = $record['department'] ?? null;

        if ($departmentName) {

            $department = $this->getOrCreateDepartment($user, $departmentName);

            if ($department instanceof Department) {
                $employee->department_id = $department->id;
                $employee->save();
            }
        }
    }
}