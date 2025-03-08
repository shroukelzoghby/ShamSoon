<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedbackRequest $request)
    {
        $user = Auth::user();
        if (!$user) {
            return errorResponse(
                message: 'unauthenticated',
                statusCode: Response::HTTP_UNAUTHORIZED
            );
        }
        $feedback = Feedback::create([
                'user_id' => $user->id,
                'name' => $request->name,
                'phone' => $request->phone,
                'message' => $request->message,
            ]);
        return successResponse(
            data: $feedback,
            message: "Feedback submitted successfully",
            statusCode: Response::HTTP_CREATED
        );

    }


}
