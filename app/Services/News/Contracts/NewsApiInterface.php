<?php

namespace App\Services\News\Contracts;

interface NewsApiInterface
{
    public function fetchArticles(array $filters): array;
}
