<?php

namespace App\Http\Controllers\API\v1\Community;

use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\API\v1\PostRequest;
use App\Http\Resources\API\v1\PostResource;
use App\Services\FirebaseNotificationService;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = Post::with('user', 'comments.user','likes')
            ->withCount('likes')
                ->latest()
                ->paginate(10);
            return successResponse(
                data: ['posts' => $posts],
                message: 'Posts retrieved successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while fetching posts.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(PostRequest $request)
    {
        try {
            $post = Auth::user()->posts()->create($request->validated());

            $users = User::whereNotNull('fcm_token')
                ->where('id', '!=', Auth::id())
                ->where('is_notify', true)
                ->get();

            $tokens = $users->pluck('fcm_token')->toArray();
            $userIds = $users->pluck('id')->toArray();

            if (!empty($tokens) && !empty($userIds)) {
                $title = 'New Post';
                $body = 'A new post has been created.';
                (new FirebaseNotificationService)->sendMulticastNotification(
                    $tokens,
                    $title,
                    $body,
                    ['post_id' => (string) $post->id],
                    $userIds
                );
            } else {
                Log::info("No users to notify for new post", ['post_id' => $post->id]);
            }

            return successResponse(
                data: ['post' => $post],
                message: 'Post created successfully and notifications sent',
                statusCode: Response::HTTP_CREATED
            );
        } catch (\Exception $e) {
            Log::error("Failed to create post or send notifications: {$e->getMessage()}", ['trace' => $e->getTraceAsString()]);
            return errorResponse(
                message: 'An error occurred while creating the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors: $e->getMessage()
            );
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {
            $post = Post::findOrFail($id);
            $post->load([
                'user',
                'comments' => function ($query) {
                    $query->with('user')->latest()->take(10);
                }
            ]);
            return successResponse(
                data: ['post' => new PostResource($post)],
                message: 'Post retrieved successfully',
                statusCode: Response::HTTP_OK
            );

        } catch (ModelNotFoundException $e) {
            return errorResponse(
                message: 'Post not found with the given ID.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while fetching the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(PostRequest $request, string $id)
    {
        try {
            $post = Post::findOrFail($id);
            $this->authorize('update', $post);
            $post->update($request->validated());
            return successResponse(
                data: ['post' => $post],
                message: 'Post updated successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return errorResponse(
                message: 'Post not found with the given ID.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        }
        catch(AuthorizationException $e)
        {
            return errorResponse(
                message:'You are not authorized to update this post.',
                statusCode:Response::HTTP_FORBIDDEN
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while updating the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $post = Post::findOrFail($id);
            $this->authorize('delete', $post);
            $post->delete();
            return successResponse(
                message: 'Post deleted successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (ModelNotFoundException $e) {
            return errorResponse(
                message: 'Post not found with the given ID.',
                statusCode: Response::HTTP_NOT_FOUND
            );
        }
        catch(AuthorizationException $e)
        {
            return errorResponse(
                message:'You are not authorized to delete this post.',
                statusCode:Response::HTTP_FORBIDDEN
            );
        }catch (\Exception $e) {
            logError('An error occurred while Deleting the post.', $e);
            return errorResponse(
                message: 'An error occurred while Deleting the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }

    }

}
