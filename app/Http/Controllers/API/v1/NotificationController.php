<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\StoreAIResultRequest;
use App\Models\Notification;
use App\Services\FirebaseNotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class NotificationController extends Controller
{

    public function index(Request $request)
    {
        try {
            $user = $request->user();
            $notifications = Notification::where('user_id', $user->id)
                ->latest()
                ->paginate(10);

            return successResponse(
                data: ['notifications' => $notifications],
                message: 'Notifications retrieved successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to retrieve notifications: ' . $e->getMessage());
            return errorResponse(
                message: 'An error occurred while retrieving notifications',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: $e->getMessage()
            );
        }
    }
    public function notifyAIResult(StoreAIResultRequest $request)
    {
        $user = $request->user();

        if ($user->is_notify && $user->fcm_token) {
            $title = 'AI Result';
            $body = $request->result;

            try {
                (new FirebaseNotificationService)->sendNotification(
                    $user->fcm_token,
                    $title,
                    $body,
                    [],
                    $user->id
                );
            } catch (\Exception $e) {
                Log::error('Failed to send Notification: ' . $e->getMessage());
                return errorResponse(
                    message: 'An error occurred while sending notification',
                    statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                    errors: $e->getMessage()
                );
            }
        }

        return successResponse(
            message: $user->is_notify ? 'AI result notification sent' : 'Notifications disabled',
            statusCode: Response::HTTP_OK
        );
    }
}
