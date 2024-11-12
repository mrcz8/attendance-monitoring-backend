<?php

namespace App\Http\Controllers;

use App\Models\LicenseKey;
use App\Models\User;
use App\Modules\Message\Message;
use App\Services\AttendanceRecordServiceInterface;
use App\Services\ClientServiceInterface;
use App\Services\LicenseKeyServiceInterface;
use Illuminate\Http\Request;

class ClientController extends Controller
{
    protected ClientServiceInterface $clientService;

    protected LicenseKeyServiceInterface $licenseKeyService;

    protected AttendanceRecordServiceInterface $attendanceRecordService;

    public function __construct(
        ClientServiceInterface $clientServiceInterface,
        LicenseKeyServiceInterface $licenseKeyServiceInterface,
        AttendanceRecordServiceInterface $attendanceRecordServiceInterface)
    {
        $this->clientService = $clientServiceInterface;
        $this->licenseKeyService = $licenseKeyServiceInterface;
        $this->attendanceRecordService = $attendanceRecordServiceInterface;
    }

    public function list(Request $request, Message $message)
    {
        $query = $request->query('q', null);
        $page = $request->query('page', null);

        $filters = [
            'q' => $query,
            'page' => $page,
        ];

        $paginatedItems = $this->clientService->list($filters);

        $message->setContent(200, 'Items retrieved', '', $paginatedItems->toArray());

        return $message->render();
    }

    public function find($id, Message $message)
    {
        $user = $this->clientService->find($id);

        if ($user instanceof User) {
            $message->setContent(200, 'Client retrieve', '', [
                'user' => $user
            ]);
        } else {
            $message->setContent(400, 'Client retrieve failed');
        }

        return $message->render();
    }

    public function store(Request $request, Message $message)
    {
        $user = $request->user();

        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8'],
            'role' => ['sometimes', 'string'],
        ]);

        $user = $this->clientService->store(
            $user,
            $data['name'],
            $data['email'],
            $data['password'],
            $data['role'] ?? null
        );

        if ($user instanceof User) {
            $message->setContent(201, 'Client created', '', [
                'Client' => $user
            ]);
        } else {
            $message->setContent(400, 'Client create failed');
        }

        return $message->render();
    }

    public function update(Request $request, Message $message, $id)
    {
        $data = $request->validate([
            'name' => ['required', 'string'],
            'email' => ['required', 'email'],
        ]);

        $user = $this->clientService->update(
            $id,
            $data['name'],
            $data['email'],
        );

        if ($user instanceof User) {
            $message->setContent(200, 'Client updated', '', [
                'user' => $user
            ]);
        } else {
            $message->setContent(400, 'Client update failed');
        }

        return $message->render();
    }

    public function deactivate($id, Message $message)
    {
        $user = $this->clientService->deactivate($id);

        if ($user instanceof User) {
            $message->setContent(200, 'Client deactivated', '', [
                'user' => $user
            ]);
        } else {
            $message->setContent(400, 'Client deactivate failed');
        }

        return $message->render();
    }

    public function restore($id, Message $message)
    {
        $user = $this->clientService->restore($id);

        if ($user instanceof User) {
            $message->setContent(200, 'Client restored', '', [
                'user' => $user
            ]);
        } else {
            $message->setContent(400, 'Client restore failed');
        }

        return $message->render();
    }

    public function delete($id, Message $message)
    {
        $user = $this->clientService->forceDelete($id);

        if ($user instanceof User) {
            $message->setContent(200, 'Client deleted', '', [
                'user' => $user
            ]);
        } else {
            $message->setContent(400, 'Client delete failed');
        }

        return $message->render();
    }

    public function onboard(Request $request, Message $message)
    {
        $client = $request->user();
        $key = $request->input('key');

        $exposedAttributes = [
            'id',
            'name',
            'role',
        ];

        $response = [
            'isLoggedIn' => true,
            'user' => $client->only($exposedAttributes),
            'session' => (object)[
                'lifetime' => intval(env('SESSION_LIFETIME', 120))
            ]
        ];

        $license = $this->licenseKeyService->onboard($client, $key);



        if ($license instanceof LicenseKey) {
            $message->setTitle('Client onboarded successfully');

            $response['user'] = array_merge($client->only($exposedAttributes), [
                'hasLicenseKey' => true,
            ]);
        } else {
            $message->setTitle('Client onboard failed');
            $message->setStatus(400);
        }

        $message->setData($response);

        return $message->render();
    }

    public function summary(Request $request)
    {
        $user = $request->user();
        $month = $request->query('month', date('m'));
        $year = $request->query('year', date('Y'));

        $filters = [
            'month' => $month,
            'year' => $year,
        ];

        $summary = $this->attendanceRecordService->summary($user, $filters);

        return response()->json($summary);
    }
}
