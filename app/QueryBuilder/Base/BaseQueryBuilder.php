<?php

namespace App\QueryBuilder\Base;

use App\Http\Helpers\QueryBuilderHelpers\FilterComposer;
use App\Http\Helpers\QueryBuilderHelpers\SortComposer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\AllowedSort;
use Spatie\QueryBuilder\QueryBuilder;

abstract class BaseQueryBuilder
{
    public function getQueryBuilder(...$args): QueryBuilder
    {
        $queryBuilder = QueryBuilder::for($this->getBuilder(...$args))
            ->allowedFilters($this->getAllowedFilters())
            ->allowedSorts($this->getAllowedSorts())
            ->defaultSort($this->getDefaultSort());

        $this->validateSelect($queryBuilder);

        return $queryBuilder;

    }

    public function getResource(Request $request): array
    {
        return [
            'filters' => FilterComposer::getFilter($this->getAllowedFiltersResponse(), $request, $this->getFilterOptions()),
            'sorts' => SortComposer::getSort($this->getAllowedSortsResponse(), $this->getDefaultSort(), $request->input('sort')),
        ];
    }

    protected function validateSelect(QueryBuilder $builder): void
    {
        if (!$builder->getQuery()->getColumns()) {
            $table = $builder->getModel()->getTable();
            $builder->select("$table.*");
        }
    }

    abstract public function getBuilder(...$args): Builder;

    abstract public function getAllowedFiltersResponse(): array;

    abstract public function getAllowedFilters(): array;

    abstract public function getFilterOptions(): array;

    abstract public function getAllowedSortsResponse(): array;

    abstract public function getAllowedSorts(): array;

    abstract public function getDefaultSort(): string|AllowedSort;
}
