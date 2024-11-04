<?php

namespace App\Services;

use App\Models\Department;
use App\Models\User;
use App\Repository\DepartmentRepositoryInterface;
use App\Utility\Paginate;

class DepartmentService implements DepartmentServiceInterface
{
    protected $departmentRepository;

    public function __construct(DepartmentRepositoryInterface $departmentRepositoryInterface)
    {
        $this->departmentRepository = $departmentRepositoryInterface;
    }

    public function list(User $user, array $filters = []): Paginate
    {
        return $this->departmentRepository->list($user, $filters);
    }

    public function find(int $id): Department
    {
        return $this->departmentRepository->find($id);
    }

    public function store(User $user, string $name, array $shiftIds = []): Department
    {
        $department = $this->departmentRepository->store($user, $name);

        if (!empty($shiftIds)) {
            $this->departmentRepository->attachShifts($department, $shiftIds);
        }

        return $department;
    }

    public function update(User $user, int $id, string $name, array $shiftIds = []): Department
    {
        $dept = Department::find($id);
        $dept->name = $name;

        $department = $this->departmentRepository->update($dept);

        $this->departmentRepository->attachShifts($department, $shiftIds);

        return $department;
    }

    public function delete(int $id): Department
    {
        $dept = Department::findOrFail($id);

        if($dept) {
            return $this->departmentRepository->delete($dept);
        }

        throw new \Exception("Cannot find department.");
    }

    public function findDepartmentByName(User $user, string $name): ?Department
    {
        return $this->departmentRepository->findDepartmentByName($user, $name);
    }
}