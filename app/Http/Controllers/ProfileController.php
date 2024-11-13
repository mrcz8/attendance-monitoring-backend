<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Modules\Message\Message;
use App\Services\UserServiceInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    protected $userService;

    public function __construct(UserServiceInterface $userServiceInterface)
    {
        $this->userService = $userServiceInterface;
    }

    public function update(Request $request, Message $message)
    {
        $user = $request->user();
        $email = $request->input('email');
        $name = $request->input('name');

        $isSuccess = $this->userService->update($user, $email, $name);

        if ($isSuccess) {
            $message->setContent(200, 'User successfully updated');
        } else {
            $message->setContent(400, 'User not updated', 'User was not updated. Please try again later.');
        }

        return $message->render();
    }

    public function changePassword(Request $request, Message $message)
    {
        $user = $request->user();
        $oldPassword = $request->input('oldPassword');
        $newPassword = $request->input('newPassword');

        $isSuccess = $this->userService->changePassword($user, $oldPassword, $newPassword);

        if ($isSuccess) {
            $message->setContent(200, 'User password changed');
        } else {
            $message->setContent(400, 'User password not changed');
        }

        return $message->render();
    }
}
