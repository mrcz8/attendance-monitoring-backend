<?php

namespace App\Repository\Eloquent;

use App\Models\AttendanceRecord;
use App\Models\Employee;
use App\Models\User;
use App\Repository\Base\BaseRepository;
use App\Repository\EmployeeRepositoryInterface;
use App\Utility\Paginate;
use Illuminate\Support\Facades\DB;

class EmployeeRepository extends BaseRepository implements EmployeeRepositoryInterface
{
    protected array $defaultFilters = [
        /**
         * Search keyword
         * This filters the items with a keyword. When this value is null, this filter is skipped.
         */
        'q' => null,

        /**
         * Search keyword
         * This filters the items with a keyword. When this value is null, this filter is skipped.
         */
        'month' => null,

        /**
         * Search keyword
         * This filters the items with a keyword. When this value is null, this filter is skipped.
         */
        'year' => null,

        /**
         * Pagination
         * The current page of items to get
         */
        'page' => 1,
    ];

    public function __construct(Employee $employee)
    {
        parent::__construct($employee);
    }

    public function list(array $filter = []): Paginate
    {
        $filters = array_merge($this->defaultFilters, array_filter($filter, fn ($f) => !is_null($f)));

        if (empty($filters['year'])) {
            $filters['year'] = AttendanceRecord::selectRaw('YEAR(MAX(date)) as year')->value('year');
        }

        if (empty($filters['month']) && !empty($filters['year'])) {
            $filters['month'] = AttendanceRecord::whereYear('date', $filters['year'])
                                                ->selectRaw('MONTH(MAX(date)) as month')
                                                ->value('month');
        }

        $items = $this->model->with(['department', 'shift', 'attendanceRecords' => function ($query) use ($filters) {
            if (!empty($filters['year'])) {
                $query->whereYear('date', $filters['year']);
            }
            if (!empty($filters['month'])) {
                $query->whereMonth('date', $filters['month']);
            }
        }]);

        // Search Filters
        if (!is_null($filters['q'])) {
            $items = $items->where(function ($q) use($filters) {
                $q->where('name', 'LIKE', '%' . $filters['q'] . '%');
            });
        }

        return new Paginate($items, self::MAX_PAGE_ITEMS, $filters['page'], 'items');
    }


    public function store(User $user, string $name, ?string $biometric, ?int $deptId, ?int $shiftId): Employee
    {
        return DB::transaction(function () use ($user, $name, $biometric, $deptId, $shiftId) {
            return $user->employees()->create([
                'name' => $name,
                'biometric_id' => $biometric,
                'department_id' => $deptId,
                'shift_id' => $shiftId,
            ]);
        });
    }

    public function find(int $id): ?Employee
    {
        $employee = $this->model->with('department', 'shift')->find($id);

        return $employee;
    }

    public function update(Employee $employee): Employee
    {
        DB::transaction(function() use($employee){
            $employee->update();
        });

        return $employee;
    }

    public function delete(Employee $employee): Employee
    {
        DB::transaction(function() use($employee) {
            $employee->delete();
        });

        return $employee;
    }
}