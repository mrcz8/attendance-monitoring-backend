<?php

namespace App\Repository;

use App\Models\Shift;
use App\Models\User;
use App\Utility\Paginate;

interface ShiftRepositoryInterface
{
    /**
     * Maximum items to be shown per page
     *
     * @var int MAX_PAGE_ITEMS
     */
    public const MAX_PAGE_ITEMS = 15;

    public function list(User $user, array $filters = []): Paginate;

    public function store(User $user, string $name, $time_in, $time_out): Shift;

    public function find(int $id): Shift;

    public function update(Shift $shift): Shift;

    public function delete(Shift $shift): Shift;
}