<?php

namespace App\Repository;

use App\Models\Employee;
use App\Models\User;
use App\Utility\Paginate;

interface EmployeeRepositoryInterface
{
    /**
     * Maximum items to be shown per page
     *
     * @var int MAX_PAGE_ITEMS
     */
    public const MAX_PAGE_ITEMS = 15;

    public function list(User $user, array $filer = []): Paginate;

    public function store(User $user, string $name, ?string $biometric, ?int $deptId, ?int $shiftId): Employee;

    public function find(int $id): ?Employee;

    public function update(Employee $employee): Employee;

    public function delete(Employee $employee): Employee;

}