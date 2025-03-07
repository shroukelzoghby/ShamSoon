<?php

namespace App\Http\Controllers\Api\v1\Auth;

use Hash;
use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\Response;


class SocialiteController extends Controller
{
    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }
    public function handleGoogleCallback()
    {
        try {
            //signed up with google before
            $user = Socialite::driver('google')->stateless()->user();
            $finduser = User::where('social_id', $user->id)->first();
            if ($finduser) {
                $token = $finduser->createToken('GoogleAuthToken')->plainTextToken;
                return successResponse(
                    data: [
                        'user' => $finduser,
                        'token' => $token,
                    ],
                    message: 'User authenticated successfully',
                    statusCode: Response::HTTP_OK
                );
            }
            //signed up normally before
            $existingUser = User::where('email', $user->email)->first();
            if ($existingUser) {
                $existingUser->update([
                    'social_id' => $user->id,
                ]);

                $token = $existingUser->createToken('GoogleAuthToken')->plainTextToken;

                return successResponse(
                    data: [
                        'user' => $existingUser,
                        'token' => $token,
                    ],
                    message: 'Google account linked successfully. Logged in!',
                    statusCode: Response::HTTP_OK
                );
            }

            $newuser = User::create([
                'username' => $user->name,
                'email' => $user->email,
                'social_id' => $user->id,
                'password' => Hash::make('google-pass')
            ]);
            $token = $newuser->createToken('GoogleAuthToken')->plainTextToken;
            return successResponse(
                data: [
                    'user' => $newuser,
                    'token' => $token,
                ],
                message: 'User authenticated successfully',
                statusCode: Response::HTTP_CREATED
            );


        } catch (Exception $e) {
            Log::error('Google Login Error: ' . $e->getMessage() . ' in ' . $e->getFile() . ' on line ' . $e->getLine() . ' with code ' . $e->getCode());
            return errorResponse(
                message: 'Google Login failed',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
