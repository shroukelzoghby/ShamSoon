<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ForgotPasswordRequest;
use App\Models\User;
use App\Notifications\ResetPasswordVerificationNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ForgetPasswordController extends Controller
{
    public function forgetPassword(ForgotPasswordRequest $request)
    {
        try {

            $input = $request->only('email');
            $user = User::where('email', $input['email'])->first();
            $user->notify(new ResetPasswordVerificationNotification());
            return successResponse(
                message: 'OTP sent successfully.',
                statusCode: Response::HTTP_OK
            );

        }catch (\Exception $e) {
            Log::error('Failed to send OTP: ' . $e->getMessage());

            return errorResponse(
                message: 'An error occurred while sending the OTP.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
