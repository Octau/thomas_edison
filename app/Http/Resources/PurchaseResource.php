<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseResource extends JsonResource
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
            'total_price'    => $this->total_price,
            'code'           => $this->code,
            'items'          => $this->items,
            'supplier'       => $this->supplier,
            'transaction_at' => $this->transaction_at,
            'created_at'     => $this->created_at,
        ];
    }
}
