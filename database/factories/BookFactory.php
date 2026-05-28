<?php

namespace Database\Factories;

use App\Models\Book;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Book>
 */
class BookFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(3),
            'author' => $this->faker->name,
            'cover_image' => $this->faker->imageUrl(),
            'price' => $this->faker->randomFloat(2, 5, 100),
            'published_date' => $this->faker->date(),
        ];
    }
}
