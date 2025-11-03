<?php

namespace App\Models\Articles;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articles extends Model
{
    use SoftDeletes;

    protected $table = 'articles';
    protected $fillable = ['url','source','author','title','description','published_at','content','type','order','category'];


    public function scopeFilterArticles($query, array $filters)
    {

        if (!empty($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (!empty($filters['source'])) {
            $query->where(function ($q) use ($filters) {
                $source = $filters['source'];
                $q->where('source', 'LIKE', "%{$source}%");
            });
        }

        if (!empty($filters['author'])) {
            $query->where(function ($q) use ($filters) {
                $author = $filters['author'];
                $q->where('author', 'LIKE', "%{$author}%");
            });
        }

        if (!empty($filters['category'])) {
            $query->where('category', $filters['category']);
        }

        if (!empty($filters['from'])) {
            $query->whereDate('published_at', '>=', $filters['from']);
        }

        if (!empty($filters['to'])) {
            $query->whereDate('published_at', '<=', $filters['to']);
        }

        if (!empty($filters['q'])) {
            $query->where(function ($q) use ($filters) {
                $search = $filters['q'];
                $q->where('title', 'LIKE', "%{$search}%")
                    ->orWhere('description', 'LIKE', "%{$search}%")
                    ->orWhere('category', 'LIKE', "%{$search}%")
                    ->orWhere('author', 'LIKE', "%{$search}%")
                    ->orWhere('content', 'LIKE', "%{$search}%");
            });
        }

        return $query->orderBy('published_at', 'asc');
    }


}
