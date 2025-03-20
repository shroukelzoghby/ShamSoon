<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Resources\API\v1\UserResource;
use App\Models\Role;
use App\Models\User;
use App\Http\Controllers\Controller;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Symfony\Component\HttpFoundation\Response;
use App\Http\Requests\API\v1\UserRegisterRequest;

class UserRegisterController extends Controller
{
    public function __invoke(UserRegisterRequest $request): JsonResponse
    {
        try {

            $user = DB::transaction(function () use ($request) {
                $roleId = Role::where('name', 'user')->value('id');

                if (!$roleId) {
                    throw new Exception('Role not found', Response::HTTP_NOT_FOUND);
                }

                // Create a new user
                return User::create(
                    $request->validated() + ['role_id' => $roleId]
                );

            });
            $token = $user->createToken('authToken')->plainTextToken;

            return successResponse(
                data:[
                    'user' => new UserResource($user) ,
                    'token' => $token,
                    ],
                message: 'User created successfully',
                statusCode: Response::HTTP_CREATED
            );

        } catch (\Exception $e) {
            logError('User registration failed', $e);
            return errorResponse(
                message: 'Registration failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors:$e->getMessage(),
            );
        }

    }
}
