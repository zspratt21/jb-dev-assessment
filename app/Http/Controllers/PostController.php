<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use App\Traits\ChecksPostsExist;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    use ChecksPostsExist;

    /**
     * @throws UnauthorizedException
     */
    public function checkPostUserId($id)
    {
        if ($id !== auth()->id()) {
            throw new UnauthorizedException;
        }
    }

    public function index(): JsonResponse
    {
        $posts = Post::paginate(10);

        return response()->json([
            'data' => $posts->items(),
            'current_page' => $posts->currentPage(),
            'total_pages' => $posts->lastPage(),
        ]);
    }

    public function store(PostCreateRequest $request): JsonResponse
    {
        $existing_post = Post::where('title', $request->title)->first();
        if ($existing_post) {
            return response()->json(['error' => "Post with title `$request->title` already exists"], 400);
        }

        return response()->json($request->user()->posts()->create($request->validated()), 201);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function show($id): JsonResponse
    {
        $this->checkPostExists($id);

        return response()->json(Post::find($id));
    }

    /**
     * @throws UnauthorizedException
     * @throws ModelNotFoundException
     */
    public function update(PostRequest $request, int $id): JsonResponse
    {
        $this->checkPostExists($id);
        $post = Post::find($id);
        $this->checkPostUserId($post->user_id);
        $post->update($request->validated());

        return response()->json($post);
    }

    /**
     * @throws UnauthorizedException
     * @throws ModelNotFoundException
     */
    public function destroy(int $id): JsonResponse
    {
        $this->checkPostExists($id);
        $post = Post::find($id);
        $this->checkPostUserId($post->user_id);
        $post->delete();

        return response()->json(['message' => "Post with $id deleted"]);
    }
}
