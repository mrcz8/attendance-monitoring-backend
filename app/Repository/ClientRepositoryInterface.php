<?php

namespace App\Repository;

use App\Models\User;
use App\Utility\Paginate;

interface ClientRepositoryInterface
{
    /**
     * Maximum items to be shown per page
     *
     * @var int MAX_PAGE_ITEMS
     */
    public const MAX_PAGE_ITEMS = 15;

    public function find($id): User;

    public function list(array $filters = []): Paginate;

    public function store(User $user): User;

    public function update($user): User;

    public function deactivate($user): User;

    public function restore($user): User;

    public function forceDelete($user): User;
}