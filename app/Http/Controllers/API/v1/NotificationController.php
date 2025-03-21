<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\StoreAIResultRequest;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{
    public function notifyAIResult(StoreAIResultRequest $request)
    {
        $user = $request->user();

        if ($user->fcm_token) {
            $title = 'AI Result';
            $body = $request->result;

            try {
                (new FirebaseNotificationService)->sendNotification($user->fcm_token, $title, $body);
            } catch (\Exception $e) {
                Log::error('Failed to send Notification ' . $e->getMessage());
                return errorResponse(
                    message: 'An error occurred while Send notification',
                    statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                    errors: $e->getMessage()
                );
            }
        }
        return successResponse(
        message: "AI result notification sent",
        statusCode: Response::HTTP_OK
        );
    }
}
