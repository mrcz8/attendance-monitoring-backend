<?php

namespace App\Repository;

use App\Models\Department;
use App\Models\User;
use App\Utility\Paginate;

interface DepartmentRepositoryInterface
{
    /**
     * Maximum items to be shown per page
     *
     * @var int MAX_PAGE_ITEMS
     */
    public const MAX_PAGE_ITEMS = 15;

    public function list(User $user, array $filters = []): Paginate;

    public function find(int $id): Department;

    public function store(User $user, string $name): Department;

    public function update(Department $dept): Department;

    public function delete(Department $dept): Department;

    public function attachShifts(Department $department, array $shiftIds): void;

    public function findDepartmentByName(User $user, string $name): ?Department;
}