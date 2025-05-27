<?php

namespace App\Http\Controllers\API\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\UpdateReminderDateTimeRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ReminderController extends Controller
{
    public function updateReminderDatetime(UpdateReminderDatetimeRequest $request)
    {
        $user = Auth::user();
        $user->reminder_date = $request->reminder_date;
        $user->save();

        return successResponse(
            data: $user->reminder_date,
            message: 'Reminder datetime updated successfully.',
            statusCode: Response::HTTP_OK
        );
    }
}
