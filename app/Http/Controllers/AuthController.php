<?php

namespace App\Http\Controllers;

use App\Modules\Message\Message;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function authenticate(Request $request, Message $message)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);
        } catch (Exception $e) {
            return $e;
        }

        $exposedAttributes = [
            'id',
            'name',
            'role',
        ];

        $message->setTitle("These credentials do not match our records");
        $message->setStatus(200);

        $response = [
            'isLoggedIn' => false,
            'user' => null,
            'session' => (object)[
                'lifetime' => intval(env('SESSION_LIFETIME', 120))
            ]
        ];

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();

            $user = $request->user();
            $licenseKey = $user->licenseKey;

            $message->setTitle("User successfully logged in");
            $response['isLoggedIn'] = true;
            $response['user'] = array_merge($user->only($exposedAttributes), [
                'hasLicenseKey' => !is_null($licenseKey),
            ]);
        }

        $message->setData($response);

        return $message->render();
    }

    public function logout(Request $request, Message $message)
    {
        Auth::guard('web')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $message->setContent(200, 'User logged out successfully');
        return $message->render();

    }
}
