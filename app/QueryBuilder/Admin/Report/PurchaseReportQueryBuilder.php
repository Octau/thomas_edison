<?php

namespace App\QueryBuilder\Admin\Report;

use App\Models\Purchase;
use App\QueryBuilder\Base\BaseQueryBuilder;
use App\QueryBuilder\Sorts\InsensitiveSort;
use Illuminate\Database\Eloquent\Builder;
use Spatie\QueryBuilder\AllowedFilter;
use Spatie\QueryBuilder\AllowedSort;

class PurchaseReportQueryBuilder extends BaseQueryBuilder
{
    public function getBuilder(...$args): Builder
    {
        $builder = Purchase::query();
        $arg = $args[0];
        $q = $arg->input('q');
        $filter = $arg->input('filter');

        if ($q) $builder->where('code', 'ILIKE', "%{$q}%");
        if ($filter) {
            foreach ($filter as $key => $item) {
                if (str_contains($key, "_after")) {
                    $customKey = str_replace("_after", "", $key);
                    $builder->where($customKey, '>=', $item);
                    continue;
                } else if (str_contains($key, "_before")) {
                    $customKey = str_replace("_before", "", $key);
                    $builder->where($customKey, '<', $item);
                    continue;
                }
                $builder->where($key, "=", $item);
            }
        }

        return $builder;
    }

    public function getAllowedFiltersResponse(): array
    {
        return [
            'partialText.code',
            'afterDate.transaction_at_after',
            'beforeDate.transaction_at_before',
        ];
    }

    public function getAllowedFilters(): array
    {
        return [
            AllowedFilter::partial('code'),
            AllowedFilter::callback('transaction_at_after', function (Builder $query, $value) {
                $query->where('transaction_at', '>=', $value);
            }),
            AllowedFilter::callback('transaction_at_before', function (Builder $query, $value) {
                $query->where('transaction_at', '<', $value);
            }),

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
            'code',
            'transaction_at',
            'created_at'
        ];
    }

    public function getAllowedSorts(): array
    {
        return [
            AllowedSort::custom('code', new InsensitiveSort()),
            AllowedSort::field('transaction_at'),
            AllowedSort::field('created_at'),
        ];
    }

    public function getDefaultSort(): AllowedSort|string
    {
        return '-transaction_at';
    }
}
