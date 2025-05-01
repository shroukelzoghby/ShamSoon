<?php

namespace App\Http\Controllers\Api\v1\community;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PostLikeController extends Controller
{
    public function like(Post $post)
    {
        try {

            $liker = Auth::user();
            if (!$liker->likes()->where('post_id', $post->id)->exists()) {   // avoid duplicate records from the api tester
                $liker->likes()->attach($post);
            }
            return successResponse(
                data: [
                    'post_id' => $post->id,
                    'liked' => true,
                    'likes_count' => $post->likes()->count()
                ],
                message: 'Post liked successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            logError('An error occurred while unliking the post.', $e);
            return errorResponse(
                message: 'An error occurred while liking the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors:$e->getMessage()
            );
        }
    }
    public function unlike(Post $post)
    {
        try {
            $liker = Auth::user();
            if ($liker->likes()->where('post_id', $post->id)->exists())  // avoid load on database
            {
                $liker->likes()->detach($post);
            }
            return successResponse(
                data: [
                    'post_id' => $post->id,
                    'liked' => false,
                    'likes_count' => $post->likes()->count()
                ],
                message: 'Post unliked successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            logError('An error occurred while unliking the post.', $e);
            return errorResponse(
                message: 'An error occurred while unliking the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR,
                errors:$e->getMessage()
            );
        }

    }
}
