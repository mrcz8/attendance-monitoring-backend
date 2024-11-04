<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use App\Modules\Message\Message;
use App\Services\EmployeeService;
use App\Services\EmployeeServiceInterface;
use Illuminate\Http\Request;

class EmployeeController extends Controller
{
    protected EmployeeService $employeeService;

    public function __construct(EmployeeServiceInterface $employeeServiceInterface)
    {
        $this->employeeService = $employeeServiceInterface;
    }

    public function list(Request $request, Message $message)
    {
        $query = $request->query('q', null);
        $page = $request->query('page', null);

        $filters = [
            'q' => $query,
            'page' => $page,
        ];

        $paginatedItems = $this->employeeService->list($filters);

        $message->setContent(200, 'Employees retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function store(Request $request, Message $message)
    {
        $user = $request->user();
        $name = $request->input('name');
        $biometric = $request->input('biometric');
        $deptId = $request->input('deptId');
        $shiftId = $request->input('shiftId');

        $employee = $this->employeeService->store($user, $name, $biometric, $deptId, $shiftId);

        if($employee instanceof Employee) {
            $message->setContent(201, 'Employee created', '', [
                'employee' => $employee
            ]);
        } else {
            $message->setContent(400, 'Employee create failed');
        }

        return $message->render();
    }

    public function find(Request $request, Message $message, $id)
    {
        $user = $request->user();

        $employee = $this->employeeService->find($id);
        $message->setContent(200, 'Employee retrieved', '', $employee->toArray());

        return $message->render();
    }

    public function update(Request $request, Message $message, $id)
    {
        $user = $request->user();
        $name = $request->input('name');
        $biometric = $request->input('biometric');
        $deptId = $request->input('deptId');
        $shiftId = $request->input('shiftId');

        $employee = $this->employeeService->update($id, $name, $biometric, $deptId, $shiftId);

        if($employee instanceof Employee) {
            $message->setContent(201, 'Employee updated', '', [
                'employee' => $employee
            ]);
        } else {
            $message->setContent(400, 'Employee update failed');
        }

        return $message->render();
    }

    public function delete(Request $request, Message $message, $id)
    {
        $employee = $this->employeeService->delete($id);

        if ($employee instanceof Employee) {
            $message->setContent(201, 'Employee deleted', '', [
                'employee' => $employee
            ]);
        } else {
            $message->setContent(400, 'Employee delete failed');
        }

        return $message->render();
    }

    public function getDeptShift(Request $request)
    {
        $user = $request->user();
        $data = $this->employeeService->getUserDepartmentsAndShifts($user);

        return response()->json($data);
    }
}
