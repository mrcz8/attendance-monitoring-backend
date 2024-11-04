<?php

namespace App\Repository;

use App\Models\Employee;
use App\Models\User;
use App\Utility\Paginate;

interface AttendanceRecordRepositoryInterface
{
    /**
     * Maximum items to be shown per page
     *
     * @var int MAX_PAGE_ITEMS
     */
    public const MAX_PAGE_ITEMS = 15;

    public function list(User $user, array $filters = []);

    public function store(Employee $employee, array $logs);
}