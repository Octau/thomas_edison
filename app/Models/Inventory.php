<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use App\Models\Common\ActivityLogName;
use App\Models\Contract\ActivityLogged;
use App\Models\Trait\HasActivityLog;

class Inventory extends BaseUuidModel implements ActivityLogged
{
    use HasActivityLog;

    public function getLogNameToUse(): ?string
    {
        return ActivityLogName::MASTER_INVENTORY;
    }

    protected  $fillable = [
        'name',
        'note',
        'type',
        'buy_price',
        'sell_price',
        'min_sell_price',
        'note',
        'amount',
    ];

    public function getRules(): array
    {
        return [
            'name'           => 'required|string|max:255',
            'note'           => 'nullable|string|max:255',
            'type'           => 'required|string|max:255',
            'buy_price'      => 'required|numeric|min:0',
            'sell_price'     => 'required|numeric|min:0|gte:min_sell_price',
            'min_sell_price' => 'required|numeric|min:0',
            'amount'         => 'required|numeric',
        ];
    }
}
