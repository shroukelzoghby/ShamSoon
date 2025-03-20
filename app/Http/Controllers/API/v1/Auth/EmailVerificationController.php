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
            $emailToVerify = $user->new_email ?? $user->email;
            if ($user->email_verified_at && !$user->new_email) {
                return errorResponse(
                    message: 'Email is already verified.',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }
            $user->notify(new EmailVerificationNotification($emailToVerify));
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
            $user = User::where('new_email', $request->email)->first();
            if ($user) {
                //The user is updating their email
                $user->email = $user->new_email;
                $user->new_email = null;
            } else {
                // The user is verifying their email for the first time
                $user = User::where('email', $request->email)->first();
            }
            if (!$user) {
                //Before verifying, they change their email again by requesting another OTP
                return errorResponse(
                    message: 'This email is not associated with a pending verification request. Please request a new OTP for your current email.',
                    statusCode: Response::HTTP_BAD_REQUEST
                );

            }
            $user->email_verified_at = now();
            $user->save();
            $message = $user->wasChanged('email')
                ? 'Email updated and verified successfully.'
                : 'Email verified successfully.';

            return successResponse(
                data: $user,
                message: $message,
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
