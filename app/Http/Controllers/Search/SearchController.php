<?php

namespace App\Http\Controllers\Search;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\SearchRequest;
use App\Http\Resources\SearchResource;
use App\Services\Search\SearchManager;

class SearchController extends Controller
{
    public function __construct(
        protected SearchManager $service,
    ) {}

    public function index(SearchRequest $request)
    {
        $param = $request->validated();
        $result = $this->service->smartSearch($param['q']);

        return SearchResource::collection($result);
    }
}
