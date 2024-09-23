<?php

namespace App\Http\Controllers;

use App\Http\Requests\PostRequest;
use App\Models\Post;

class PostController extends Controller
{
    public function index()
    {
        return Post::all();
    }

    public function store(PostRequest $request)
    {
        return Post::create($request->validated());
    }

    public function show(Post $post)
    {
        return $post;
    }

    public function update(PostRequest $request, Post $post)
    {
        $post->update($request->validated());

        return $post;
    }

    public function destroy(Post $post)
    {
        $post->delete();

        return response()->json();
    }
}
