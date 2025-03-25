<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
            'phone' => '1234567890',
            'password' => bcrypt('password')
        ]);

        // Create authors
        $authors = User::factory()->count(5)->author()->create();

        // Create regular users
        $users = User::factory()->count(10)->user()->create();

        // Create categories
        $categories = Category::factory()->count(10)->create();

        // Create posts for each author
        $authors->each(function ($author) use ($categories) {
            Post::factory()
                ->count(5)
                ->create(['user_id' => $author->id])
                ->each(function ($post) use ($categories) {
                    // Attach random categories to each post
                    $post->categories()->attach(
                        $categories->random(rand(1, 3))->pluck('id')->toArray()
                    );

                    // Create comments for each post
                    Comment::factory()
                        ->count(rand(3, 8))
                        ->create(['post_id' => $post->id]);
                });
        });
    }
}
