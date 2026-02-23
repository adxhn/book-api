<?php

namespace App\Services\Search;

use App\Models\Book;
use Illuminate\Support\Facades\Cache;

class SearchManager
{
    public function smartSearch(string $param)
    {
        if (Cache::has($param)) {
            return Cache::get($param);
        }

        $result = $this->relevance($param);

        if (mb_strlen($param) > 5) {
            Cache::put($param, $result, 300);
        }

        return $result;
    }

    /**
     * Yazılan her kelime text içinde mutlaka geçer.
     * Kelime sonuna ek alsa da yakalar (Wildcard).
     * Kelime sırası önemli değil.
     */
    protected function matchAll(string $param)
    {
        $searchableTerm = collect(explode(' ', $param))
            ->filter() // Boşlukları temizler
            ->map(fn($term) => "+$term*")
            ->implode(' ');

        return Book::whereFullText('title', $searchableTerm, ['mode' => 'boolean'])->limit(10)->get();
    }

    /**
     * booleanSearch'ın gelişmiş halidir ve skora göre sıralama yapar.
     * Düşük skorlu veriyi eler.
     */
    protected function relevance(string $param)
    {
        return Book::select('*')
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
    protected function exactPhrase(string $param)
    {
        $exactTerm = '"' . $param . '"';
        return Book::whereFullText('title', $exactTerm, ['mode' => 'boolean'])->limit(10)->get();
    }
}
