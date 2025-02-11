<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\API\v1\UserRegisterRequest;

class UserRegisterController extends Controller
{
    public function __invoke(UserRegisterRequest $request)
    {
        try {
            $validated = $request->validated();
            $user = User::create([
                'username' => $validated['username'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'password' => $validated['password']

            ]);
            $token = $user->createToken('authToken')->plainTextToken;
            return successResponse(
                data: [
                    'user' => $user,
                    'token' => $token,
                ],
                message: 'Registration successful',
                statusCode: Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            logError('User registration failed', $e);
            return errorResponse(
                message: 'Registration failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }
}
