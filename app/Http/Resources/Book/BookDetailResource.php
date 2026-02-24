<?php

namespace App\Http\Resources\Book;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

class BookDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'title' => $this->title,
            'slug' => $this->slug,
            'isbn' => $this->isbn,
            'image_url' => $this->image_url,
            'publish_date' => Carbon::create($this->publish_date)->format('d.m.Y'),
            'category' => [
                'name' => $this->category->name,
            ],
            'publisher' => new PublisherResource($this->publisher),
            'author' => new AuthorResource($this->author)
        ];
    }
}
