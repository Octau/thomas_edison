<?php

namespace App\QueryBuilder\Sorts;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class BelongsToSort extends InsensitiveSort
{
    public function __construct(
        private string  $related,
        private ?string $foreignKey = null,
        private ?string $ownerKey = null,
    )
    {
        $this->foreignKey = $this->foreignKey ?: Str::snake(class_basename($this->related)) . '_id';
        $this->ownerKey = $this->ownerKey ?: 'id';
    }

    public function __invoke(Builder $query, bool $descending, string $property): void
    {
        $master = $query->getModel()->getTable();
        $relation = Str::snake(Str::pluralStudly(class_basename($this->related)));

        $query->leftJoin($relation, "{$master}.{$this->foreignKey}", '=', "{$relation}.{$this->ownerKey}");

        parent::__invoke($query, $descending, $property);
    }
}
