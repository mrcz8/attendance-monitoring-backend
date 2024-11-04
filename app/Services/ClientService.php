<?php

namespace App\Services;

use App\Models\User;
use App\Repository\ClientRepositoryInterface;
use App\Utility\Paginate;

class ClientService implements ClientServiceInterface
{
    protected $clientRepository;

    public function __construct(ClientRepositoryInterface $clientRepositoryInterface)
    {
        $this->clientRepository = $clientRepositoryInterface;
    }

    public function find($id): User
    {
        return $this->clientRepository->find($id);
    }

    public function list(array $filters = []): Paginate
    {
        return $this->clientRepository->list($filters);
    }

    public function store($user, $name, $email, $password, $role): User
    {
        if ($user->role === 'super_admin') {
            $role = 'admin';
        }

        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = bcrypt($password);
        $user->role = $role;

        return $this->clientRepository->store($user);
    }

    public function update($id, $name, $email): User
    {
        $user = User::find($id);
        $user->name = $name;
        $user->email = $email;
        return $this->clientRepository->update($user);
    }

    public function deactivate($id): User
    {
        $user = User::find($id);
        return $this->clientRepository->deactivate($user);
    }

    public function restore($id): User
    {
        $user = User::withTrashed()->find($id);
        return $this->clientRepository->restore($user);
    }

    public function forceDelete($id): User
    {
        $user = User::find($id);
        return $this->clientRepository->forceDelete($user);
    }
}