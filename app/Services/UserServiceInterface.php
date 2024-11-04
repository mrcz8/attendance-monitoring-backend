<?php

namespace App\Services;

use App\Models\User;

interface UserServiceInterface
{
    public function list(array $filter);

    public function store($user, $name, $email, $password, $role): User;
}