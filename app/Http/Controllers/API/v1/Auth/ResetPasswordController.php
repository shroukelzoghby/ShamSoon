<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\ResetPasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class ResetPasswordController extends Controller
{
    public function resetPassword(ResetPasswordRequest $request)
    {
        try {
            $user = User::where('email', $request->email)->first();
            $user->update(['password' => Hash::make($request->password)]);

            return successResponse(
                message: 'Password reset successfully.',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            Log::error('Failed to reset password: ' . $e->getMessage());

            return errorResponse(
                message: 'An error occurred while resetting the password.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
