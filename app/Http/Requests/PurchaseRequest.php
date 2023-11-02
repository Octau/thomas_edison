<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class PurchaseRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'supplier_id'                 => 'required|exists:suppliers,id',
            'total_price'                 => 'required|numeric|min:0',
            'transaction_at'              => 'required|date',
            'items'                       => 'required|array',
            'items.*.item.id'             => 'nullable|exists:inventories,id',
            'items.*.item.name'           => 'required|string|max:255',
            'items.*.item.note'           => 'nullable|string|max:255',
            'items.*.type'                => 'required|in:new,add',
            'items.*.item.buy_price'      => 'required|numeric|min:0',
            'items.*.item.min_sell_price' => 'required|numeric|gte:items.*.item.buy_price',
            'items.*.item.sell_price'     => 'required|numeric|gte:items.*.item.min_sell_price',
            'items.*.item.amount'         => 'required|numeric|min:0',
            'items.*.item.type'           => 'required|string|max:255',
        ];
    }
}
