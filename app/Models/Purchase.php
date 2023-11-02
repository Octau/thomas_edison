<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use App\Models\Trait\HasActivityLog;

class Purchase extends BaseUuidModel
{
    use HasActivityLog;

    protected $fillable = [
        'total_price',
        'supplier_id',
        'created_by',
        'items',
        'code',
        'transaction_at',
    ];

    protected $casts = [
        'items'         => 'array',
    ];

    public function getRules(): array
    {
        return [
            'total_price' => 'required|numeric|min:0',
        ];
    }

    public function supplier() {
        return $this->belongsTo('App\Models\Supplier');
    }

    public function user() {
        return $this->belongsTo('App\Models\User');
    }
}
