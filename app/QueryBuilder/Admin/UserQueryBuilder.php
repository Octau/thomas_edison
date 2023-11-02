<?php

namespace App\QueryBuilder\Admin;

use App\Models\User;
use App\QueryBuilder\Base\BaseQueryBuilder;
use App\QueryBuilder\Sorts\InsensitiveSort;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class UserQueryBuilder extends BaseQueryBuilder
{
    public function getBuilder(...$args): Builder
    {
        $builder = User::query();

        if ($args[0]) $builder->where('name', 'ILIKE', "%{$args[0]}%");
        return $builder;
    }

    public function getAllowedFiltersResponse(): array
    {
        return [
            'partialText.name',
        ];
    }

    public function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
        ];
    }

    public function getFilterOptions(): array
    {
        $options = [];
        return $options;
    }

    public function getAllowedSortsResponse(): array
    {
        return [
            'name',
        ];
    }

    public function getAllowedSorts(): array
    {
        return [
            AllowedSort::custom('name', new InsensitiveSort()),
        ];
    }

    public function getDefaultSort(): AllowedSort|string
    {
        return 'name';
    }
}
