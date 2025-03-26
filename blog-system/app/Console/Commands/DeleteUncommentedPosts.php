<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteUncommentedPosts extends Command
{
    protected $signature = 'posts:delete-uncommented';

    protected $description = 'Delete posts that have not received any comments in the last week';

    public function handle()
    {
        $posts = Post::whereDoesntHave('comments', function ($query) {
            $query->where('created_at', '>=', now()->subWeek());
        })->get();

        foreach ($posts as $post) {
            $post->delete();
            Log::info("Deleted uncommented post: {$post->title}");
        }

        $this->info("Deleted {$posts->count()} uncommented posts");
    }
}
