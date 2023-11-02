<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use App\Models\Trait\HasActivityLog;

class Customer extends BaseUuidModel
{
    use HasActivityLog;

    protected $fillable = [
        'name',
        'phone',
        'address',
    ];

    public function getRules(): array
    {
        return [
            'address' => 'required|string|max:255',
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
        ];
    }
}
