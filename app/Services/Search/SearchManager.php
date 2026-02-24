<?php

namespace App\Services\Search;

use App\Models\Author;
use App\Models\Book;
use App\Models\Publisher;
use Illuminate\Support\Facades\Cache;

class SearchManager
{
    public function smartSearch(string $param)
    {
        $cacheKey = 'search_' . $param;

        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }

        $books = $this->relevanceBooks($param);
        $authors = $this->authors($param);
        $publishers = $this->publishers($param);

        $result = [
            'books' => $books,
            'authors' => $authors,
            'publishers' => $publishers,
        ];

        Cache::put($cacheKey, $result, 300);

        return $result;
    }

    /**
     * Yazılan her kelime text içinde mutlaka geçer.
     * Kelime sonuna ek alsa da yakalar (Wildcard).
     * Kelime sırası önemli değil.
     */
    protected function matchAllBooks(string $param)
    {
        $searchableTerm = collect(explode(' ', $param))
            ->filter() // Boşlukları temizler
            ->map(fn($term) => "+$term*")
            ->implode(' ');

        return Book::with(['category', 'author', 'publisher'])->whereFullText('title', $searchableTerm, ['mode' => 'boolean'])->limit(10)->get();
    }

    /**
     * booleanSearch'ın gelişmiş halidir ve skora göre sıralama yapar.
     * Düşük skorlu veriyi eler.
     */
    protected function relevanceBooks(string $param)
    {
        return Book::with(['category', 'author', 'publisher'])->select('*')
            ->selectRaw(
                "MATCH(title) AGAINST(? IN BOOLEAN MODE) as score",
                [$param]
            )
            ->whereFullText('title', $param)
            ->having('score', '>', 2)
            ->orderByDesc('score')
            ->limit(10)
            ->get();
    }

    /**
     * Kullanıcının yazdığı kelimeler tam olarak o sırayla bulunur.
     * Like aramasına benzer bir yapıda (kullanıcı deneyimi düşük kalabilir)
     */
    protected function exactPhraseBooks(string $param)
    {
        $exactTerm = '"' . $param . '"';
        return Book::whereFullText('title', $exactTerm, ['mode' => 'boolean'])->limit(10)->get();
    }

    protected function authors(string $param)
    {
        return Author::where('name', 'LIKE', $param . '%')->limit(10)->get();
    }

    protected function publishers(string $param)
    {
        return Publisher::where('name', 'LIKE', $param . '%')->limit(10)->get();
    }
}
