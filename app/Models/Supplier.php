<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;

class Supplier extends BaseUuidModel
{
    protected $fillable = [
        'name',
        'phone',
        'address',
        'is_active',
    ];

    public function getRules(): array
    {
        return [
            'address' => 'required|string|max:255',
            'is_active' => 'boolean',
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
        ];
    }
}
