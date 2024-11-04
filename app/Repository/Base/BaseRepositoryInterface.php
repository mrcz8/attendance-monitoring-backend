<?php

namespace App\Repository\Base;

use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Model;

/**
 * interface EloquentRepositoryInterface
 * @package App\Repository\Eloquent
 *
 * Interface for Eloquen Repositories
 */
interface BaseRepositoryInterface
{
    /**
     * Retrieves all the instances of Model
     *
     * @return Collection
     */
    public function all(): Collection;

    /**
     * Find a Model instance using ID
     *
     * @param int $id
     *
     * @return null|Model
     */
    public function find(int $id): ?Model;
}