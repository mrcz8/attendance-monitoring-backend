<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use App\Utility\Paginate;

interface DepartmentServiceInterface
{
    public function list(User $user, array $filters = []): Paginate;

    public function find(int $id): Department;

    public function store(User $user, string $name, array $shiftIds = []): Department;

    public function update(User $user, int $id, string $name, array $shiftIds = []): Department;

    public function delete(int $id): Department;

    public function findDepartmentByName(User $user, string $name): ?Department;
}