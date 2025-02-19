<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\CheckOTPRequest;
use Ichtrojan\Otp\Otp;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class CheckOTPController extends Controller
{
    public function verifyOTP(CheckOTPRequest $request, Otp $otp)
    {
        try {
            $otpValidation = $otp->validate($request->email, $request->otp);
            if (!$otpValidation->status) {
                return errorResponse(
                    message: 'Invalid or expired OTP. Please try again.',
                    statusCode: Response::HTTP_UNAUTHORIZED
                );
            }

            return successResponse(
                message: 'OTP verified successfully.',
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
