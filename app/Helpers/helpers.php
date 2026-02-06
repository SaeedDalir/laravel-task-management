<?php

use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

if (! function_exists('format_pagination')) {
    function format_pagination(AnonymousResourceCollection $collection): array
    {
        return $collection
            ->response()
            ->getData(true);
    }
}
