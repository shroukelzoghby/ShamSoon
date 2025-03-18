<?php

namespace App\Http\Controllers\API\v1\Community;

use App\Http\Requests\API\v1\CommentRequest;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseNotificationService;
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

            $post = Post::find($request->post_id);
            $postOwner = User::find($post->user_id);

            if ($postOwner->fcm_token) {
                $title = 'New Comment';
                $body = 'Someone commented on your post.';
                (new FirebaseNotificationService)->sendNotification($postOwner->fcm_token, $title, $body);
            }
            return successResponse(
                data: ['comment' => $comment],
                message: 'Comment created successfully and Notification sent',
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
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request, Comment $comment)
    {
        try {
            $this->authorize('update', $comment);
            $comment->update($request->validated());
            return successResponse(
                data: ['comment' => $comment],
                message: 'comment updated successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while updating the comment.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment,Post $post)
    {
        try {
            $this->authorize('delete', $comment);
            $comment->delete();
            return successResponse(
                message: 'comment deleted successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while deleting the comment.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
