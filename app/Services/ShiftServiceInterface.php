<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\User;
use App\Utility\Paginate;

interface ShiftServiceInterface
{
    public function list(User $user, array $filters = []): Paginate;

    public function store(User $user, string $name, $time_in, $time_out): Shift;

    public function find(int $id): Shift;

    public function update(User $user, int $id, string $name, $time_in, $time_out): Shift;

    public function delete(int $id): Shift;
}