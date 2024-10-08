<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Book>
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
            'name' => $this->faker->sentence(3),
            'description' => $this->faker->sentence(),
            'number_of_pages' => $this->faker->numberBetween(100, 1000),
            'number_of_copies' => $this->faker->numberBetween(1, 100),
            'isbn' => $this->faker->isbn13(),
            'language' => $this->faker->randomElement(['English', 'Spanish', 'French', 'German', 'Japanese']),
            'binding' => $this->faker->randomElement(['Hardcover', 'Paperback', 'Spiral-bound']),
            'script' => $this->faker->randomElement(['Cyrillic', 'Latin', 'Arabic']),
            'dimensions' => $this->faker->randomElement(['21cm x 29.7cm', '15cm x 21cm', 'A1', 'A2', 'A3']),
        ];
    }
}
