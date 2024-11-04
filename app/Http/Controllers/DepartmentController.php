<?php

namespace App\Http\Controllers;

use App\Models\Department;
use App\Modules\Message\Message;
use App\Services\DepartmentServiceInterface;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    protected $departmentService;

    public function __construct(DepartmentServiceInterface $departmentServiceInterface)
    {
        $this->departmentService = $departmentServiceInterface;
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

        $paginatedItems = $this->departmentService->list($user, $filters);

        $message->setContent(200, 'Departments retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function find(Request $request, $id, Message $message)
    {
        $user = $request->user();

        $paginatedItems = $this->departmentService->find($id);

        $message->setContent(200, 'Department retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function store(Request $request, Message $message)
    {
        $user = $request->user();
        $name = $request->input('name');
        $shiftIds = $request->input('shifts', []);

        $department = $this->departmentService->store($user, $name, $shiftIds);

        if ($department instanceof Department) {
            $message->setContent(201, 'Department created', '', [
                'department' => $department
            ]);
        } else {
            $message->setContent(400, 'Department create failed');
        }

        return $message->render();
    }

    public function update(Request $request, $id, Message $message)
    {
        $user = $request->user();
        $name = $request->input('name');
        $shiftIds = $request->input('shifts', []);

        $department = $this->departmentService->update($user, $id, $name, $shiftIds);

        if ($department instanceof Department) {
            $message->setContent(201, 'Department created', '', [
                'department' => $department
            ]);
        } else {
            $message->setContent(400, 'Department create failed');
        }

        return $message->render();
    }

    public function delete(Request $request, Message $message, $id)
    {
        $department = $this->departmentService->delete($id);

        if ($department instanceof Department) {
            $message->setContent(201, 'Department deleted', '', [
                'department' => $department
            ]);
        } else {
            $message->setContent(400, 'Department delete failed');
        }

        return $message->render();
    }
}
