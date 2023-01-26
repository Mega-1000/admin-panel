<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;

class PaginationHelper
{
    public static function paginateModelsGroupBy(Collection $collection, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $offsetEnd = $offset + $perPage;

        $allThreadsNumber = $collection->count();
        $numberOfPages = ceil($allThreadsNumber / $perPage);
        $chunk = $collection->slice($offset, $offsetEnd)->toArray();

        $result = [
            'numberOfPages' => intval($numberOfPages),
            'chunk'         => $chunk,
        ];

        return $result;
    }
}
