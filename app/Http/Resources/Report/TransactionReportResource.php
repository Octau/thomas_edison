<?php

namespace App\Http\Resources\Report;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TransactionReportResource extends JsonResource
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
            'created_at'     => Carbon::parse($this->created_at)->format('d-m-Y H:i:s'),
        ];
    }
}
