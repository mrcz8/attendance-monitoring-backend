<?php

namespace App\Services;

use App\Models\User;

interface UserServiceInterface
{
    public function list(array $filter);

    public function store($user, $name, $email, $password, $role): User;

    public function update(User $user, string $email, string $name): bool;

    public function changePassword(User $user, string $oldPassword, string $newPassword): bool;
}