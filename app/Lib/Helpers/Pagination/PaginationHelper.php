<?php


namespace App\Lib\Helpers\Pagination;


use Illuminate\Pagination\LengthAwarePaginator;

class PaginationHelper
{

    public static function arrayPaginator($array, $request, $perPage)
    {
        $currentPage = $request['page'] ? $request['page'] : 1;
        $offset = ($currentPage * $perPage) - $perPage;
        return new LengthAwarePaginator(array_slice($array, $offset, $perPage, true), count($array), $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]);
    }
}
