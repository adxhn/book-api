<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchRequest;
use App\Http\Resources\Search\AuthorResource;
use App\Http\Resources\Search\BookResource;
use App\Http\Resources\Search\PublisherResource;
use App\Services\SearchManager;

class SearchController extends Controller
{
    public function __construct(
        protected SearchManager $service,
    ) {}

    public function index(SearchRequest $request)
    {
        $param = $request->validated();
        $result = $this->service->smartSearch($param['q']);

        return $this->success(data:
            [
                'books' => BookResource::collection($result['books']),
                'authors' => AuthorResource::collection($result['authors']),
                'publishers' => PublisherResource::collection($result['publishers']),
            ]);
    }
}
