<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\StoreFCMTokenRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class UserController extends Controller
{
    public function storeFcmToken(StoreFCMTokenRequest $request)
    {
        $user = $request->user();

        $user->fcm_token = $request->fcm_token;
        $user->save();
        return successResponse(
            data: ['user' => $user],
            message: 'FCM token stored successfully',
            statusCode: Response::HTTP_OK
        );

    }

    public function setNotificationPermission(Request $request)
    {
        $request->validate([
            'is_notify' => 'required|boolean',
        ]);

        $user = $request->user();
        $user->is_notify = $request->is_notify;
        $user->save();

        return successResponse(
            data: ['is_notify' => $user->is_notify],
            message: 'Notification permission updated successfully',
            statusCode: Response::HTTP_OK
        );
    }

    public function getNotificationPermission(Request $request)
    {
        $user = $request->user();
        return successResponse(
            data: ['is_notify' => $user->is_notify],
            message: 'Notification permission retrieved successfully',
            statusCode: Response::HTTP_OK
        );
    }

}
