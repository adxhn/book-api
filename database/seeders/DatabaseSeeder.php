<?php

namespace Database\Seeders;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $map = [
            'category'    => \App\Models\Category::class,
            'genres'      => \App\Models\Genre::class,
            'collections' => \App\Models\Collection::class,
            'countries'   => \App\Models\Country::class,
            'themes'      => \App\Models\Theme::class,
            'moods'       => \App\Models\Mood::class,
        ];

        DB::transaction(function () use ($map) {
            foreach ($map as $key => $model) {
                $data = collect(config("taxonomy.$key"))->map(fn($item) => [
                    'name'       => $item['name'],
                    'slug'       => Str::slug($item['name']),
                ])->toArray();

                $model::insert($data);
            }
        });

        Publisher::factory(100)->create();
        Author::factory(100)->create();

        Book::factory(1000)
            ->create();
    }
}
