<?php

namespace App\Http\Controllers;

use App\Models\LicenseKey;
use App\Models\User;
use App\Modules\Message\Message;
use App\Services\LicenseKeyServiceInterface;
use App\Services\UserServiceInterface;
use Illuminate\Http\Request;

class UserController extends Controller
{
    protected $userService;

    protected $licenseKeyService;

    public function __construct(UserServiceInterface $userServiceInterface, LicenseKeyServiceInterface $licenseKeyServiceInterface)
    {
        $this->userService = $userServiceInterface;
        $this->licenseKeyService = $licenseKeyServiceInterface;
    }

    public function onboard(Request $request, Message $message)
    {
        $user = $request->user();

        $licenseKey = $request->validate([
            'key' => ['required', 'string'],
        ]);

        $key = $this->licenseKeyService->onboard($user, $licenseKey['key']);

        if ($key instanceof LicenseKey) {
            $message->setContent(201, 'License key created', '', [
                'Key' => $key
            ]);
        } else {
            $message->setContent(400, 'License key create failed');
        }

        return $message->render();
    }
}
