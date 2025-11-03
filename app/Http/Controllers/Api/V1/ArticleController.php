<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Api\MainApiController;
use App\Http\Requests\Api\Article\ArticleFilterRequest;
use App\Http\Resources\Api\Article\ArticleCollection;
use App\Http\Responses\V1\CustomResponse;
use Illuminate\Http\Response;

class ArticleController extends MainApiController
{


    public function index(ArticleFilterRequest $request){

        $type = $request->input('type', 'news_api');

        $filters = $request->only(['q', 'from', 'to','category','source','author','type']);

        $factory = app('servicesV1');
        $provider = $factory::make($type);

        $articles = $provider->fetchArticles($filters);

        $data = (new ArticleCollection($articles))->toArray();

        return CustomResponse::Success(Response::HTTP_OK, 'Data uploaded successfully', $data['articles'], []);
    }
}
