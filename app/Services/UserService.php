<?php

namespace App\Services;

use App\Models\User;
use App\Repository\UserRepositoryInterface;
use Exception;
use Illuminate\Support\Facades\Hash;

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

    public function update(User $user, string $email, string $name): bool
    {
        return $this->userRepository->update($user->id, $email, $name);
    }

    public function changePassword(User $user, string $oldPassword, string $newPassword): bool
    {
        if (Hash::check($oldPassword, $user->password)) {
            return $this->userRepository->changePassword($user->id, $newPassword);
        } else {
            throw new Exception('Incorrect old password given when changing password');
        }
    }
}
