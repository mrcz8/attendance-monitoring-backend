<?php

namespace App\Repository\Eloquent;

use App\Models\Shift;
use App\Models\User;
use App\Repository\Base\BaseRepository;
use App\Repository\ShiftRepositoryInterface;
use App\Utility\Paginate;
use Illuminate\Support\Facades\DB;

class ShiftRepository extends BaseRepository implements ShiftRepositoryInterface
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

    public function __construct(Shift $shift)
    {
        parent::__construct($shift);
    }

    public function list(User $user, array $userFilters = []): Paginate
    {
        $items = $this->model->where('user_id', $user->id);
        $filters = array_merge($this->defaultFilters, array_filter($userFilters, fn ($f) => !is_null($f)));

        if (!is_null($filters['q'])) {
            $items = $items->where(function ($q) use($filters) {
                $q
                    ->where('name', 'LIKE', '%' . $filters['q'] . '%')
                    ->where('email', 'LIKE', '%' . $filters['q'] . '%');
            });
        }

        return new Paginate($items, self::MAX_PAGE_ITEMS, $filters['page'], 'items');
    }

    public function store(User $user, string $name, $time_in, $time_out): Shift
    {
        return DB::transaction(function () use ($user, $name, $time_in, $time_out) {
            return $user->shifts()->create([
                'name' => $name,
                'time_in' => $time_in,
                'time_out' => $time_out,
            ]);
        });
    }

    public function find(int $id): Shift
    {
        return $this->model->findOrFail($id);
    }

    public function update(Shift $shift): Shift
    {
        DB::transaction(function() use($shift){
            $shift->save();
        });

        return $shift;
    }

    public function delete(Shift $shift): Shift
    {
        DB::transaction(function() use($shift){
            $shift->delete();
        });

        return $shift;
    }

}