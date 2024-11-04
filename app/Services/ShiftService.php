<?php

namespace App\Services;

use App\Models\Shift;
use App\Models\User;
use App\Repository\ShiftRepositoryInterface;
use App\Utility\Paginate;

class ShiftService implements ShiftServiceInterface
{
    protected $shiftRepository;

    public function __construct(ShiftRepositoryInterface $shiftRepositoryInterface)
    {
        $this->shiftRepository = $shiftRepositoryInterface;
    }

    public function list(User $user, array $filters = []): Paginate
    {
        return $this->shiftRepository->list($user, $filters);
    }

    public function store(User $user, string $name, $time_in, $time_out): Shift
    {
        return $this->shiftRepository->store($user, $name, $time_in, $time_out);
    }

    public function find(int $id): Shift
    {
        return $this->shiftRepository->find($id);
    }

    public function update(User $user, int $id, string $name, $time_in, $time_out): Shift
    {
        $shift = Shift::find($id);
        $shift->name = $name;
        $shift->time_in = $time_in;
        $shift->time_out = $time_out;

        return $this->shiftRepository->update($shift);
    }

    public function delete(int $id): Shift
    {
        $shift = Shift::findOrFail($id);

        if($shift) {
            return $this->shiftRepository->delete($shift);
        }

        throw new \Exception("Cannot find Shift.");
    }
}