<?php

namespace App\Http\Controllers;

use App\Exceptions\ModelNotFoundException;
use App\Http\Requests\CommentRequest;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use App\Traits\ChecksPostsExist;
use App\Traits\ChecksUsersExist;
use Illuminate\Database\Eloquent\ModelNotFoundException as EloquentModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    use ChecksPostsExist, ChecksUsersExist;

    /**
     * @throws ModelNotFoundException
     */
    public function checkCommentExistsAndOwned(User $user, $id): void
    {
        try {
            $user->comments()->findOrFail($id);
        } catch (EloquentModelNotFoundException $e) {
            throw new ModelNotFoundException("Comment with id $id not found or is someone else's comment");
        }
    }

    /**
     * @throws ModelNotFoundException
     */
    public function indexByPost(int $post_id): JsonResponse
    {
        $this->checkPostExists($post_id);
        $post = Post::find($post_id);
        $comments = $post->comments()->paginate(10);

        return response()->json([
            'data' => $comments->items(),
            'current_page' => $comments->currentPage(),
            'total_pages' => $comments->lastPage(),
        ]);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function indexByUser(int $user_id): JsonResponse
    {
        $this->checkUserExists($user_id);
        $user = User::find($user_id);
        $comments = $user->comments()->paginate(10);

        return response()->json([
            'data' => $comments->items(),
            'current_page' => $comments->currentPage(),
            'total_pages' => $comments->lastPage(),
        ]);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function store(CommentRequest $request, int $post_id): JsonResponse
    {
        $this->checkPostExists($post_id);

        return response()->json($request->user()->comments()->create(array_merge($request->validated(), ['post_id' => $post_id])), 201);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function update(CommentRequest $request, int $id): JsonResponse
    {
        $this->checkCommentExistsAndOwned($request->user(), $id);
        $comment = Comment::find($id);
        $comment->update($request->validated());

        return response()->json($comment);
    }

    /**
     * @throws ModelNotFoundException
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        $this->checkCommentExistsAndOwned($request->user(), $id);
        Comment::destroy($id);

        return response()->json(['message' => "Comment with id $id deleted"]);
    }
}
