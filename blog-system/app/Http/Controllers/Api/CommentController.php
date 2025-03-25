<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        $comments = Comment::with(['user', 'post'])->get();
        return response()->json($comments);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'post_id' => 'required|exists:posts,id',
            'content' => 'required|string'
        ]);

        $comment = new Comment();
        $comment->post_id = $validated['post_id'];
        $comment->user_id = auth()->id();
        $comment->content = $validated['content'];
        $comment->save();

        return response()->json($comment->load(['user', 'post']), 201);
    }

    public function show(Comment $comment)
    {
        return response()->json($comment->load(['user', 'post']));
    }

    public function update(Request $request, Comment $comment)
    {
        $validated = $request->validate([
            'content' => 'required|string'
        ]);

        $comment->content = $validated['content'];
        $comment->save();

        return response()->json($comment->load(['user', 'post']));
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
        return response()->json(null, 204);
    }

    public function approve(Comment $comment)
    {
        $comment->approved_at = now();
        $comment->save();

        return response()->json($comment->load(['user', 'post']));
    }

    public function reject(Comment $comment)
    {
        $comment->approved_at = null;
        $comment->save();

        return response()->json($comment->load(['user', 'post']));
    }
}
