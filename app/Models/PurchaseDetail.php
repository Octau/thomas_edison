<?php

namespace App\Models;

use App\Models\Abstract\BaseUuidModel;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PurchaseDetail extends BaseUuidModel
{
    protected  $fillable = [
        'qty', 'buy_price'
    ];
    
    public function getRules(): array
    {
        return [
            'qty' => 'required|numeric|min:1',
            'buy_price' => 'required|numeric|min:0',
        ];
    }

    public function inventory(){
        return $this->hasOne('App\Models\Inventory');
    }

    public function purchase(){
        return $this->belongsTo('App\Models\Purchase');
    }
}
