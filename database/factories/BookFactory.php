<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

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
            'title' => $this->faker->sentence(3),
            'slug' => $this->faker->slug(3),
            'description' => $this->faker->text(),
            'isbn' => $this->faker->isbn13(),
            'image_url' => $this->faker->imageUrl(),
            'publish_date' => $this->faker->date(),
            'page_number' => random_int(100, 300),
            'category_id' => random_int(1, 19),
            'author_id' => random_int(1, 50),
            'publisher_id' => random_int(1, 50),
        ];
    }
}
