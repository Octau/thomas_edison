<?php

namespace App\Http\Resources\Report;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseReportResource extends JsonResource
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
            'transaction_at' => Carbon::parse($this->transaction_at)->format('d-m-Y H:i:s'),
            'created_at'     => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
        ];
    }
}
