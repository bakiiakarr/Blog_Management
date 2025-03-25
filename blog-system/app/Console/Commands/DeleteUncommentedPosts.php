<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class DeleteUncommentedPosts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'posts:delete-uncommented';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete posts that have not received any comments in the last week';

    /**
     * Execute the console command.
     */
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
