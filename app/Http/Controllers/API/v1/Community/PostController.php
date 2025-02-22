<?php

namespace App\Http\Controllers\API\v1\Community;

use App\Http\Controllers\Controller;
use App\Http\Requests\API\v1\PostRequest;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $posts = Post::with('user', 'comments.user')->latest()->get();
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
            return successResponse(
                data: ['post' => $post],
                message: 'Post created successfully',
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
    public function show(Post $id)
    {
        try {
            return successResponse(
                data: ['post' => $id->load('user', 'comments.user')],
                message: 'Post retrieved successfully',
                statusCode: Response::HTTP_OK
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
    public function update(PostRequest $request, Post $post)
    {
        try {
            $this->authorize('update', $post);
            $post->update($request->validated());
            return successResponse(
                data: ['post' => $post],
                message: 'Post updated successfully',
                statusCode: Response::HTTP_OK
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
    public function destroy(Post $post)
    {
        try {
            $this->authorize('delete', $post); // Ensure the user owns the post
            $post->delete();
            return successResponse(
                message: 'Post deleted successfully',
                statusCode: Response::HTTP_OK
            );
        } catch (\Exception $e) {
            return errorResponse(
                message: 'An error occurred while deleting the post.',
                statusCode: Response::HTTP_INTERNAL_SERVER_ERROR
            );
        }
    }

}
