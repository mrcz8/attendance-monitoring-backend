<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Repository\Eloquent\EmployeeRepository;
use App\Repository\EmployeeRepositoryInterface;
use App\Utility\Paginate;

class EmployeeService implements EmployeeServiceInterface
{
    protected EmployeeRepository $employeeRepository;

    public function __construct(EmployeeRepositoryInterface $employeeRepositoryInterface)
    {
        $this->employeeRepository = $employeeRepositoryInterface;
    }

    public function list(array $filter = []): Paginate
    {
        return $this->employeeRepository->list($filter);
    }

    public function getUserDepartmentsAndShifts(User $user): array
    {
        $user->load([
            'departments:id,user_id,name',
            'shifts:id,user_id,name'
        ]);

        return [
            'departments' => $user->departments->map(function ($department) {
                return [
                    'id' => $department->id,
                    'name' => $department->name,
                ];
            }),
            'shifts' => $user->shifts->map(function ($shift) {
                return [
                    'id' => $shift->id,
                    'name' => $shift->name,
                ];
            }),
        ];
    }

    public function store(User $user, string $name, ?string $biometric, ?int $deptId, ?int $shiftId): Employee
    {
        return $this->employeeRepository->store($user, $name, $biometric, $deptId, $shiftId);
    }

    public function find(int $id): ?Employee
    {
        return $this->employeeRepository->find($id);
    }

    public function update(int $id, string $name, ?string $biometric, int $deptId, int $shiftId): Employee
    {
        $employee = Employee::findOrFail($id);
        $employee->name = $name;
        $employee->biometric_id = $biometric;
        $employee->department_id = $deptId;
        $employee->shift_id = $shiftId;

        return $this->employeeRepository->update($employee);
    }

    public function delete(int $id): Employee
    {
        $employee = Employee::findOrFail($id);
        return $this->employeeRepository->delete($employee);
    }
}