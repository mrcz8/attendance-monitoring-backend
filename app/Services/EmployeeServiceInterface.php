<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\User;
use App\Utility\Paginate;

interface EmployeeServiceInterface
{
    public function list(User $user, array $filters = []): Paginate;

    public function getUserDepartmentsAndShifts(User $user): array;

    public function store(User $user, string $name, ?string $biometric, ?int $deptId, ?int $shiftId): Employee;

    public function find(int $id): ?Employee;

    public function update(int $id, string $name, ?string $biometric, int $deptId, int $shiftId): Employee;

    public function delete(int $id): Employee;
}