<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserBookResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'read_status' => $this->read_status,
            'read_count' => $this->read_count,
            'title' => $this->book->title,
            'book_slug' => $this->book->slug,
            'image_url' => $this->book->image_url,
            'category' => $this->book->category->name,
            'category_slug' => $this->book->category->slug,
            'author' => $this->book->author->name,
            'author_slug' => $this->book->author->slug,
            'publisher' => $this->book->author->name,
            'publisher_slug' => $this->book->author->slug
        ];
    }
}
