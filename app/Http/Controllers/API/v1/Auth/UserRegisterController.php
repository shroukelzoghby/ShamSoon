<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\API\v1\UserRegisterRequest;

class UserRegisterController extends Controller
{
    public function __invoke(UserRegisterRequest $request)
    {
        try {
            $user = User::create([
                'username' => $request->username,
                'email' => $request->email,
                'phone' => $request->phone,
                'password' => Hash::make($request->password)
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
