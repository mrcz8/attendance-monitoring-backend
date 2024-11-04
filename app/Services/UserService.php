<?php

namespace App\Services;

use App\Models\User;
use App\Repository\UserRepositoryInterface;

class UserService implements UserServiceInterface
{
    protected $userRepository;

    public function __construct(UserRepositoryInterface $userRepositoryInterface)
    {
        $this->userRepository = $userRepositoryInterface;
    }

    public function list($filter)
    {
        return $this->userRepository->list($filter);
    }

    public function store($user, $name, $email, $password, $role): User
    {
        if ($user->role === 'super_admin') {
            $role = 'admin';
        } else {
            $role = $requestedRole ?? 'user';
        }

        return $this->userRepository->store($name, $email, $password, $role);
    }
}
