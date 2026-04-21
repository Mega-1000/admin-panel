<?php

namespace App\Helpers;

use Illuminate\Database\Eloquent\Collection;

class PaginationHelper
{
    /**
     * @param Collection $collection
     * @param int $page
     * @param int $perPage
     * @return array
     */
    public static function paginateModelsGroupBy(Collection $collection, int $page, int $perPage): array
    {
        $offset = ($page - 1) * $perPage;
        $offsetEnd = $offset + $perPage;

        $allThreadsNumber = $collection->count();
        $numberOfPages = ceil($allThreadsNumber / $perPage);
        $chunk = $collection->slice($offset, $offsetEnd)->toArray();

        return [
            'numberOfPages' => intval($numberOfPages),
            'chunk'         => $chunk,
        ];
    }
}
