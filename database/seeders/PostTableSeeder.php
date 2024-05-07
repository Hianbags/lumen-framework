<?php

namespace Database\Seeders;

use App\Models\Post;
use Illuminate\Database\Seeder;

class PostTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create 10 posts
        \Illuminate\Database\Eloquent\Factories\Factory::factoryForModel(Post::class)->count(10)->create();
    }
}
