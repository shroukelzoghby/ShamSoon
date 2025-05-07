<?php

namespace App\Http\Controllers\API\v1\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\UserAuthRequest;
use App\Http\Resources\API\v1\UserResource;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;


class UserAuthController extends Controller
{
    public function authenticate(UserAuthRequest $request)
    {
       try{
            $credentials = $request->only('email', 'password');
            $user = User::where('email', $request->email)->first();
            if (!Auth::attempt($credentials)||!$user) {
                return errorResponse(
                    message: 'Invalid login credentials',
                    statusCode: Response::HTTP_BAD_REQUEST
                );
            }
            $token = $user->createToken('authToken')->plainTextToken;
            return successResponse(
                data: [
                    'user' => new UserResource($user),
                    'token' => $token,
                ],
                message: 'Login successful',
                statusCode: Response::HTTP_OK
            );


        }catch (Exception $e){
            Log::error('Login failed: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' with code '.$e->getCode());
            return errorResponse(
                message:'Login failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }
    public function logout(Request $request)
    {
        try{
            $request->user()->tokens()->delete();
            return successResponse(
                message: 'Logout successfully',
                statusCode: Response::HTTP_OK
            );
        }catch (Exception $e){
            Log::error('Logout failed: '.$e->getMessage().' in '.$e->getFile().' on line '.$e->getLine().' with code '.$e->getCode());
            return errorResponse(
                message: 'Logout failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }


    }
}
