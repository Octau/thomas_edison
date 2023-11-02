<?php

namespace App\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Spatie\QueryBuilder\Sorts\Sort;

class MorphOneSort implements Sort
{
    public function __construct(
        private string  $related,
        private string  $name,
        private bool    $isNumber = false,
        private ?string $baseKey = null,
        private ?string $base = null,
        private ?string $morphKey = null,
        private ?string $morphType = null
    )
    {
        $this->morphKey = $this->morphKey ?: Str::lower($this->name) . '_id';
        $this->morphType = $this->morphType ?: Str::lower($this->name) . '_type';
        $this->baseKey = $this->baseKey ?: 'id';
    }

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $master = $query->getModel()->getTable();
        $relation = Str::snake(Str::pluralStudly(class_basename($this->related)));
        $this->base = $this->base ? class_basename($this->base): Str::studly(Str::singular($master));

        $orderRawSQL = $this->isNumber ? "COALESCE($property, 0)" : "LOWER($property)";

        $query->leftJoin($relation, function ($builder) use ($master, $relation) {
            $builder->on("{$master}.{$this->baseKey}", '=', "{$relation}.{$this->morphKey}")
                ->where("$relation.{$this->morphType}", '=', $this->base);
        })->orderBy(DB::raw($orderRawSQL), ($descending ? 'desc' : 'asc'));
    }
}
