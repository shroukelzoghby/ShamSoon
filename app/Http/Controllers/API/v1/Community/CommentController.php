<?php

namespace App\Http\Controllers\API\v1\Community;

use App\Http\Requests\API\v1\CommentRequest;
use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CommentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Post $post)
    {
        try {
            $comments = $post->comments()->with('user')->latest()->get();
            return successResponse(
                data: ['Comments' => $comments],
                message: 'Comments retrieved successfully', 
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while fetching Comments.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(CommentRequest $request,Post $post)
    {
        try {
            $validatedData = $request->validated(); 
            $validatedData['user_id'] = Auth::id();
            $comment = $post->comments()->create($validatedData);
            return successResponse(
                data: ['comment' => $comment],
                message: 'Comment created successfully',
                statusCode: Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while creating the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
