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
        $title = $this->faker->sentence();

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'description' => $this->faker->text(),
            'isbn' => $this->faker->isbn13(),
            'image_url' => $this->faker->imageUrl(),
            'publish_date' => $this->faker->date(),
            'page_number' => random_int(100, 300),
            'category_id' => \App\Models\Category::inRandomOrder()->first()?->id,
            'author_id' => \App\Models\Author::inRandomOrder()->first()?->id,
            'publisher_id' => \App\Models\Publisher::inRandomOrder()->first()?->id
        ];
    }
}
