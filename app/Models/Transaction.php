<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use App\Models\Trait\HasActivityLog;


class Transaction extends BaseUuidModel
{
    use HasActivityLog;

    protected  $fillable = [
        'total_price',
        'created_by',
        'items',
        'code',
        'customer_id',
    ];

    protected $casts = [
        'items'         => 'array',
    ];

    public function getRules(): array
    {
        return [
            'total_price' => 'required|numeric|min:0',
            'customer_id' => 'nullable|uuid|exists:customers,id',
        ];
    }

    public function customer() {
        return $this->belongsTo('App\Models\Customer');
    }
}
