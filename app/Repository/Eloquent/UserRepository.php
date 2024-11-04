<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Base\BaseRepository;
use App\Repository\UserRepositoryInterface;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository implements UserRepositoryInterface
{

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function list(array $filter)
    {
        return User::all();
    }

    public function store($name, $email, $password, $role): User
    {
        $user = new User();
        $user->name = $name;
        $user->email = $email;
        $user->password = $password;
        $user->role = $role;

        DB::transaction(function() use($user){
            $user->save();
        });

        return $user;
    }
}