<?php

namespace App\Http\Controllers\Api\v1;

use App\Http\Requests\Api\v1\ProfileDestroyRequest;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\Api\v1\ProfileUpdateRequest;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationController;

class ProfileController extends Controller
{
    public function update(ProfileUpdateRequest $request)
    {
        //No changes
        $user = $request->user();
        $validated = $request->validated();
        $user->fill($validated);
        if (!$user->isDirty()) {
            return successResponse(

                message: 'No changes',
                statusCode: Response::HTTP_OK
            );
        }
        //save with a change in email
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
            $user->new_email = $validated['email'];  // temporary field for changing emails 
            $user->save();
            app(EmailverificationController::class)->sendEmailVerification($request);
            return successResponse(
                message: 'A verification email has been sent. Please verify to complete the update.',
                statusCode: Response::HTTP_OK
            );
        }
        //save with no change in email
        $user->save();

        return successResponse(
            data: $user,
            message: 'profile updated successfully',
            statusCode: Response::HTTP_OK
        );

    }

    public function destroy(ProfileDestroyRequest $request)
    {
        $user = $request->user();

        $user->tokens()->delete();

        $user->forceDelete();

        return successResponse(
            message: 'Your account has been permanently deleted.',
            statusCode: Response::HTTP_OK
        );
    }

}
