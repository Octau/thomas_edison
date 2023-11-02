<?php

namespace App\Models\Abstract;

use Illuminate\Database\Eloquent\Builder;
use RuntimeException;

abstract class BaseCompositeModel extends BaseModel
{
    public $incrementing = false;

    protected $keyType = 'array';

    protected $primaryKey = [];

    public function getKeyName(): array
    {
        return $this->primaryKey;
    }

    public function getKey(): array
    {
        $attributes = [];

        foreach ($this->getKeyName() as $key) {
            $attributes[$key] = $this->getAttribute($key);
        }

        return $attributes;
    }

    protected function setKeysForSaveQuery($query): Builder
    {
        foreach ($this->getKeyName() as $key) {
            if (isset($this->$key)) {
                $query->where($key, '=', $this->$key);
            } else {
                throw new RuntimeException(__METHOD__ . 'Missing part of the primary key: ' . $key);
            }
        }

        return $query;
    }
}
