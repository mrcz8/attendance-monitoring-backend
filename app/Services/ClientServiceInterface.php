<?php

namespace App\Services;

use App\Models\User;
use App\Utility\Paginate;

interface ClientServiceInterface
{
    public function find($id): User;

    public function list(array $filters = []): Paginate;

    public function store($user, $name, $email, $password, $role): User;

    public function update($id, $name, $email): User;

    public function deactivate($id): User;

    public function restore($id): User;

    public function forceDelete($id): User;

}