<?php

namespace App\Repository\Eloquent;

use App\Models\Department;
use App\Models\User;
use App\Repository\Base\BaseRepository;
use App\Repository\DepartmentRepositoryInterface;
use App\Utility\Paginate;
use Illuminate\Support\Facades\DB;

class DepartmentRepository extends BaseRepository implements DepartmentRepositoryInterface
{
    protected array $defaultFilters = [
        /**
         * Search keyword
         * This filters the items with a keyword. When this value is null, this filter is skipped.
         */
        'q' => null,

        /**
         * Pagination
         * The current page of items to get
         */
        'page' => 1,
    ];

    public function __construct(Department $department)
    {
        parent::__construct($department);
    }

    public function list(User $user, array $userFilters = []): Paginate
    {
        $items = $this->model->where('user_id', $user->id)->with('shifts');
        $filters = array_merge($this->defaultFilters, array_filter($userFilters, fn ($f) => !is_null($f)));

        // Search Filters
        if (!is_null($filters['q'])) {
            $items = $items->where(function ($q) use($filters) {
                $q
                    ->where('name', 'LIKE', '%' . $filters['q'] . '%')
                    ->where('email', 'LIKE', '%' . $filters['q'] . '%');
            });
        }

        return new Paginate($items, self::MAX_PAGE_ITEMS, $filters['page'], 'items');
    }

    public function find(int $id): Department
    {
        $department = $this->model->with('shifts')->findOrFail($id);

        return $department;
    }

    public function store(User $user, string $name): Department
    {
        return DB::transaction(function () use ($user, $name) {
            return $user->departments()->create([
                'name' => $name,
            ]);
        });
    }

    public function update(Department $dept): Department
    {
        DB::transaction(function() use($dept){
            $dept->save();
        });

        return $dept;
    }

    public function delete(Department $dept): Department
    {
        DB::transaction(function() use($dept){
            $dept->delete();
        });

        return $dept;
    }

    public function attachShifts(Department $department, array $shiftIds): void
    {
        $department->shifts()->sync($shiftIds);
    }

    public function findDepartmentByName(User $user, string $name): ?Department
    {
        $department = $this->model
            ->where('user_id', $user->id)
            ->where('name', $name)
            ->first();

        return $department;
    }
}