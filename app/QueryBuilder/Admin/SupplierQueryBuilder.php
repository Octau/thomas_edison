<?php

namespace App\QueryBuilder\Admin;

use App\Models\Supplier;
use App\QueryBuilder\Base\BaseQueryBuilder;
use App\QueryBuilder\Sorts\InsensitiveSort;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class SupplierQueryBuilder extends BaseQueryBuilder
{
    public function getBuilder(...$args): Builder
    {
        $builder = Supplier::query();

        if ($args[1]) $builder->where('name', 'ILIKE', "%{$args[1]}%");
        return $builder;
    }

    public function getAllowedFiltersResponse(): array
    {
        return [
            'partialText.name',
            'singleOption.is_active',
        ];
    }

    public function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('name'),
            AllowedFilter::exact('is_active'),
        ];
    }

    public function getFilterOptions(): array
    {
        $options = [];
        $options['is_active'] = [
            ['value' => true, 'label' => 'True'],
            ['value' => false, 'label' => 'False'],
        ];
        return $options;
    }

    public function getAllowedSortsResponse(): array
    {
        return [
            'name',
            'is_active',
        ];
    }

    public function getAllowedSorts(): array
    {
        return [
            AllowedSort::custom('name', new InsensitiveSort()),
            AllowedSort::field('is_active'),
        ];
    }

    public function getDefaultSort(): AllowedSort|string
    {
        return 'name';
    }
}
