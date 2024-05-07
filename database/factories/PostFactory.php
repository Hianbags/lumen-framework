<?php

namespace Database\Factories;

use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

class PostFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Post::class;

    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'content' => $this->faker->text,
            'image' => $this->faker->imageUrl(),
            'view' => $this->faker->randomNumber(),
            'tag' => $this->faker->word,
            'rating' => $this->faker->randomFloat(2, 0, 5),
            'comment' => $this->faker->paragraph,
            'status' => $this->faker->randomElement([0,1,2]),
            'user_id' => $this->faker->randomElement([1,9,10]),
        ];
    }
}
