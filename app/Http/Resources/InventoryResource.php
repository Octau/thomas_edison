<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class InventoryResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'buy_price'      => $this->buy_price,
            'sell_price'     => $this->sell_price,
            'min_sell_price' => $this->min_sell_price,
            'type'           => $this->type,
            'note'           => $this->note,
            'amount'         => $this->amount,
        ];
    }
}
