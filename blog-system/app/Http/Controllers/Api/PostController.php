<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with(['user', 'categories'])->get();
        return response()->json($posts);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'cover' => 'nullable|image|max:2048'
        ]);

        $post = new Post();
        $post->title = $validated['title'];
        $post->content = $validated['content'];
        $post->user_id = auth()->id();

        if ($request->hasFile('cover')) {
            $path = $request->file('cover')->store('posts', 'public');
            $post->cover = $path;
        }

        $post->save();
        $post->categories()->attach($validated['categories']);

        return response()->json($post->load(['user', 'categories']), 201);
    }

    public function show(Post $post)
    {
        return response()->json($post->load(['user', 'categories']));
    }

    public function update(Request $request, Post $post)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'categories' => 'required|array',
            'categories.*' => 'exists:categories,id',
            'cover' => 'nullable|image|max:2048'
        ]);

        $post->title = $validated['title'];
        $post->content = $validated['content'];

        if ($request->hasFile('cover')) {
            if ($post->cover) {
                Storage::disk('public')->delete($post->cover);
            }
            $path = $request->file('cover')->store('posts', 'public');
            $post->cover = $path;
        }

        $post->save();
        $post->categories()->sync($validated['categories']);

        return response()->json($post->load(['user', 'categories']));
    }

    public function destroy(Post $post)
    {
        if ($post->cover) {
            Storage::disk('public')->delete($post->cover);
        }

        $post->categories()->detach();
        $post->delete();

        return response()->json(null, 204);
    }

    public function publish(Post $post)
    {
        $post->published_at = now();
        $post->save();

        return response()->json($post->load(['user', 'categories']));
    }

    public function unpublish(Post $post)
    {
        $post->published_at = null;
        $post->save();

        return response()->json($post->load(['user', 'categories']));
    }
}
