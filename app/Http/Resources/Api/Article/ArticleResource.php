<?php

namespace App\Http\Resources\Api\Article;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArticleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request = null): array
    {
        return [
            'title' => $this['title'] ?? null,
            'source' => $this['source'] ?? null,
            'url' => $this['url'] ?? null,
            'published_at' => $this['published_at'] ?? null,
            'author' => $this['author'] ?? null,
            'description' => $this['description'] ?? null,
            'content' => $this['content'] ?? null,
            'category' => $this['category'] ?? null,
            'type' => $this['type'] ?? null,
        ];
    }
}
