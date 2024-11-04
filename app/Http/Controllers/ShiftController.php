<?php

namespace App\Http\Controllers;

use App\Models\Shift;
use App\Modules\Message\Message;
use App\Services\ShiftServiceInterface;
use Illuminate\Http\Request;

class ShiftController extends Controller
{
    protected $shiftService;

    public function __construct(ShiftServiceInterface $shiftServiceInterface)
    {
        $this->shiftService = $shiftServiceInterface;
    }

    public function list(Request $request, Message $message)
    {
        $user = $request->user();
        $query = $request->query('q', null);
        $page = $request->query('page', null);

        $filters = [
            'q' => $query,
            'page' => $page,
        ];

        $paginatedItems = $this->shiftService->list($user, $filters);

        $message->setContent(200, 'Shift retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function store(Request $request, Message $message)
    {
        $user = $request->user();
        $name = $request->input('name');
        $time_in = $request->input('time_in');
        $time_out = $request->input('time_out');

        $shift = $this->shiftService->store($user, $name, $time_in, $time_out);

        if ($shift instanceof Shift) {
            $message->setContent(201, 'Shift created', '', [
                'shift' => $shift
            ]);
        } else {
            $message->setContent(400, 'Shift create failed');
        }

        return $message->render();
    }

    public function find(Request $request, $id, Message $message)
    {
        $paginatedItems = $this->shiftService->find($id);
        $message->setContent(200, 'Shift retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function update(Request $request, Message $message, $id)
    {
        $user = $request->user();
        $name = $request->input('name');
        $time_in = $request->input('time_in');
        $time_out = $request->input('time_out');

        $shift = $this->shiftService->update($user, $id, $name, $time_in, $time_out);

        if ($shift instanceof Shift) {
            $message->setContent(200, 'Shift updated', '', [
                'shift' => $shift
            ]);
        } else {
            $message->setContent(400, 'Shift update failed');
        }

        return $message->render();
    }

    public function delete(Message $message, $id)
    {
        $shift = $this->shiftService->delete($id);

        if ($shift instanceof Shift) {
            $message->setContent(201, 'Shift deleted', '', [
                'shift' => $shift
            ]);
        } else {
            $message->setContent(400, 'Shift delete failed');
        }

        return $message->render();
    }
}
