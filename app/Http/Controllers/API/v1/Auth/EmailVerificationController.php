<?php

namespace App\Http\Controllers\Api\V1\Auth;

use App\Models\User;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\CheckOTPRequest;
use Symfony\Component\HttpFoundation\Response;
use App\Notifications\Auth\EmailVerificationNotification;

class EmailVerificationController extends Controller
{
    public function sendEmailVerification(Request $request)
    {
        try {
            $user = $request->user();
            if ($user->email_verified_at) {
                return errorResponse(
                    message: 'Email is already verified.',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }

            $user->notify(new EmailVerificationNotification());
            return successResponse(
                message: 'Verification email sent successfully.',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            logError('Failed to send email verification.', $e);
            return errorResponse(message: 'An error occurred while sending the verification email.', statusCode: Response::HTTP_INTERNAL_SERVER_ERROR);
        }

    }
    public function VerifyEmail(CheckOTPRequest $request, Otp $otp)
    {
        try {
            $otpValidation = $otp->validate($request->email, $request->otp);
            if (!$otpValidation->status) {
                return errorResponse(
                    message: 'Invalid or expired OTP. Please try again.',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }
            $user = User::where('email', $request->email)->first();
            $user->update(['email_verified_at' => now()]);
            return successResponse(
                message: 'Email verified successfully.',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('OTP verification failed for email: ' . $request->email . ' - ' . $e->getMessage());

            return errorResponse(
                message: 'Something went wrong while verifying OTP. Please try again later.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
