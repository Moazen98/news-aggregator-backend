<?php

namespace App\Services\News;

use App\Services\News\Contracts\NewsApiInterface;
use App\Services\News\Providers\NewsAIApiProvider;
use App\Services\News\Providers\NewsApiProvider;
use App\Services\News\Providers\GuardianApiProvider;
use App\Services\News\Providers\NewYorkApiProvider;
use InvalidArgumentException;

class NewsApiFactory
{
    public static function make(string $type): NewsApiInterface
    {
        switch ($type) {
            case 'news_api':
                return new NewsApiProvider();
            case 'guardian':
                return new GuardianApiProvider();
            case 'new_york':
                return new NewYorkApiProvider();
            case 'news_api_ai':
                return new NewsAIApiProvider();
            default:
                throw new InvalidArgumentException("Unknown source [$type]");
        }
    }
}
