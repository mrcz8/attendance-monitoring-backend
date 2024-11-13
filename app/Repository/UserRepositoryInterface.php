<?php

namespace App\Repository;

use App\Models\User;

interface UserRepositoryInterface
{
    public function list(array $filter);

    public function store($name, $email, $password, $role): User;

    public function update(int $id, string $email, string $name): bool;

    public function changePassword(int $id, string $newPassword): bool;
}