<?php

namespace App\Http\Resources\Api\Article;

use App\Models\Articles\Articles;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ArticleCollection extends ResourceCollection
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray($request=null)
    {
        return [
            'articles' => $this->collection->map(function ($article) use ($request) {
                return (new ArticleResource($article->resource))->toArray($request);
            })
        ];
    }
}
