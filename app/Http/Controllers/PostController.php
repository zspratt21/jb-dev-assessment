<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Exceptions\UnauthorizedException;
use App\Http\Requests\PostCreateRequest;
use App\Http\Requests\PostRequest;
use App\Models\Post;
use Illuminate\Database\Eloquent\ModelNotFoundException as EloquentModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    /**
     * @throws ModelNotFoundException
     */
    public function checkPostExists($id)
    {
        try {
            Post::findOrFail($id);
        } catch (EloquentModelNotFoundException $e) {
            throw new ModelNotFoundException("Post with id $id not found");
        }
    }

    /**
     * @throws UnauthorizedException
     */
    public function checkPostUserId($id)
    {
        if ($id !== auth()->id()) {
            throw new UnauthorizedException();
        }

        return true;
    }

    public function index()
    {
        return Post::all();
    }

    public function store(PostCreateRequest $request)
    {
        $existing_post = Post::where('title', $request->title)->first();
        if ($existing_post) {
            return response()->json(['error' => "Post with title `$request->title` already exists"], 400);
        }

        return $request->user()->posts()->create($request->validated());
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

        return response()->json(Post::find($id));
    }

    /**
     * @throws UnauthorizedException
     * @throws ModelNotFoundException
     */
    public function destroy(int $id)
    {
        $this->checkPostExists($id);
        $post = Post::find($id);
        $this->checkPostUserId($post->user_id);
        $post->delete();

        return response()->json(['message' => "Post with $id deleted"]);
    }
}
