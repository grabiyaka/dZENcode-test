<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Models\User;
use App\Models\Post;
use Faker\Factory as FakerFactory;

class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $faker = FakerFactory::create(); // Создайте экземпляр класса Faker

        return [
            'user_id' => User::inRandomOrder()->first()->id,
            'content' => $faker->paragraph,
            'created_at' => now(),
            'updated_at' => now(),
            'parent_id' => rand(0, 1) ? null : Post::inRandomOrder()->first()->id,
        ];
    }
}
