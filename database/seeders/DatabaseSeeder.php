<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use Carbon\Carbon;
use App\Models\Post;
use Illuminate\Database\Seeder;
use Database\Factories\PostFactory;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // \App\Models\User::factory(10)->create();

        // \App\Models\User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
        $faker = \Faker\Factory::create();

        for ($i = 0; $i < 10000; $i++) {
            Post::factory()->create([
                'title' => $faker->sentence(mt_rand(2, 8)),
                'slug' => $faker->slug(),
                'content' => $faker->paragraph(),
                'author_id' => mt_rand(3, 4),
                'category_id' => 4,
                'published_at' => Carbon::now()->toDateTimeString(),
            ]);
        }

        // Post::factory()->create(1);
    }
}
