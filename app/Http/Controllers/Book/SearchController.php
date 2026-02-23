<?php

namespace App\Http\Controllers\Book;

use App\Http\Controllers\Controller;
use App\Http\Requests\Book\SearchRequest;
use App\Models\Book;
use App\Services\Search\SearchManager;

class SearchController extends Controller
{
    public function __construct(
        protected SearchManager $service,
    ) {}

    public function index(SearchRequest $request)
    {
        $param = $request->validated();
        return $this->service->smartSearch($param['q']);
    }
}
