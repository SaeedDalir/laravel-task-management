<?php

namespace App\Providers;

use App\Enums\SearchEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\ServiceProvider;

class QueryBuilderServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Builder::macro('paginateWithSize', function (): LengthAwarePaginator {
            return $this->paginate(request(SearchEnum::SIZE->value, 15));
        });

        Builder::macro('getWithSize', function (?int $size = null): Collection {
            return $this->limit(request($size ?: SearchEnum::SIZE->value, 25))->get();
        });
    }
}
