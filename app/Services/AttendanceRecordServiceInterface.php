<?php

namespace App\Services;

use App\Models\User;
use App\Utility\Paginate;

interface AttendanceRecordServiceInterface
{
    public function list(User $user, array $filters = []): Paginate;

    public function store(User $user, array $data): array;

    public function summary(User $user, array $filters = []);
}