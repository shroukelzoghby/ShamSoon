<?php

namespace App\Http\Controllers\API\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\StoreFeedbackRequest;
use App\Models\Feedback;
use App\Models\User;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class FeedbackController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreFeedbackRequest $request)
    {
        $user = User::find($request->user_id);
        if (!$user) {
            return errorResponse(
                message: "User not found",
                statusCode: Response::HTTP_NOT_FOUND
            );
        }
        $feedback = Feedback::create($request->validated());
        return successResponse(
            data: $feedback,
            message: "Feedback submitted successfully",
            statusCode: Response::HTTP_CREATED
        );

    }


}
