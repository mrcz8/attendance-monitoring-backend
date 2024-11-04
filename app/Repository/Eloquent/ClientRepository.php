<?php

namespace App\Repository\Eloquent;

use App\Models\User;
use App\Repository\Base\BaseRepository;
use App\Repository\ClientRepositoryInterface;
use App\Utility\Paginate;
use Illuminate\Support\Facades\DB;

class ClientRepository extends BaseRepository implements ClientRepositoryInterface
{
    protected array $defaultFilters = [
        /**
         * Search keyword
         * This filters the items with a keyword. When this value is null, this filter is skipped.
         */
        'q' => null,

        /**
         * Pagination
         * The current page of items to get
         */
        'page' => 1,
    ];

    public function __construct(User $user)
    {
        parent::__construct($user);
    }

    public function find($id): User
    {
        return User::find($id);
    }

    public function list(array $userFilters = []): Paginate
    {
        $items = $this->model->with('licenseKey')->withTrashed();

        $filters = array_merge($this->defaultFilters, array_filter($userFilters, fn ($f) => !is_null($f)));
        $items = $items->where('role', '!=', 'super_admin');
        // Search Filters
        if (!is_null($filters['q'])) {
            $items = $items->where(function ($q) use($filters) {
                $q
                    ->where('name', 'LIKE', '%' . $filters['q'] . '%')
                    ->where('email', 'LIKE', '%' . $filters['q'] . '%');
            });
        }


        return new Paginate($items, self::MAX_PAGE_ITEMS, $filters['page'], 'items');
    }

    public function store(User $user): User
    {
        DB::transaction(function() use($user){
            $user->save();
        });

        return $user;
    }

    public function update($user): User
    {
        DB::transaction(function() use($user){
            $user->save();
        });

        return $user;
    }

    public function deactivate($user): User
    {
        DB::transaction(function() use($user){
            $user->delete();
        });

        return $user;
    }

    public function restore($user): User
    {
        DB::transaction(function() use($user){
            $user->restore();
        });

        return $user;
    }

    public function forceDelete($user): User
    {
        DB::transaction(function() use($user){
            $user->forceDelete();
        });

        return $user;
    }
}