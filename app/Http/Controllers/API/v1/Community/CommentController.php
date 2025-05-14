<?php

namespace App\Http\Controllers\API\v1\Community;

use App\Http\Requests\API\v1\CommentRequest;
use App\Models\Post;
use App\Models\Comment;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\FirebaseNotificationService;
use Exception;
use Illuminate\Auth\Access\AuthorizationException;
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
        } catch (Exception $e) {
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
            $comment = $post->comments()->create([
                'content' => $request->content,
                'user_id' => Auth::id(),
            ]);

            $postOwner = $post->user;
            if ($postOwner->is_notify && $postOwner->fcm_token) {
                $title = 'New Comment';
                $body = 'Someone commented on your post.';
                (new FirebaseNotificationService)->sendNotification(
                    $postOwner->fcm_token,
                    $title,
                    $body,
                    ['post_id' =>  (string) $post->id],
                    $postOwner->id
                );
            }
            return successResponse(
                data: ['comment' => $comment],
                message: 'Comment created successfully and Notification sent',
                statusCode: Response::HTTP_CREATED
            );
        } catch (Exception $e) {
            return errorResponse(
                message: 'An error occurred while creating the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors:$e->getMessage(),
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(CommentRequest $request,Post $post, Comment $comment)
    {
        try {
            if ($comment->post_id !== $post->id) {
                return errorResponse(
                    message: 'Comment not found for this post',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }
            $this->authorize('update', $comment);
            $comment->update($request->validated());
            return successResponse(
                data: ['comment' => $comment],
                message: 'comment updated successfully',
                statusCode: Response::HTTP_OK
            );
        }
        catch(AuthorizationException $e)
        {
            return errorResponse(
                message:'You are not authorized to update this comment.',
                statusCode:Response::HTTP_FORBIDDEN
            );
        }
        catch (Exception $e) {
            return errorResponse(
                message: 'An error occurred while updating the comment.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Post $post,Comment $comment)
    {
        try {
            if ($comment->post_id !== $post->id) {
                return errorResponse(
                    message: 'Comment not found for this post',
                    statusCode: Response::HTTP_NOT_FOUND
                );
            }
            $this->authorize('delete', $comment);
            $comment->delete();
            return successResponse(
                message: 'comment deleted successfully',
                statusCode: Response::HTTP_OK
            );
        }
        catch(AuthorizationException $e)
        {
            return errorResponse(
                message:'You are not authorized to delete this comment.',
                statusCode:Response::HTTP_FORBIDDEN
            );
        } catch (Exception $e) {
            return errorResponse(
                message: 'An error occurred while deleting the comment.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }
}
