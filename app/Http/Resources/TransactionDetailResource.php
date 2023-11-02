<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'                  => $this->id,
            'qty'                 => $this->qty,
            'buy_price'           => $this->buy_price,
            'sell_price'          => $this->sell_price,
            'min_sell_price'      => $this->min_sell_price,
        ];
    }
}
