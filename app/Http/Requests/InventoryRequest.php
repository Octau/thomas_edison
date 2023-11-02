<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class InventoryRequest extends FormRequest
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
            'name'           => 'required|string|max:255',
            'note'           => 'nullable|string|max:255',
            'type'           => 'required|string|max:255',
            'buy_price'      => 'required|numeric|min:0',
            'sell_price'     => 'required|numeric|min:0|gte:min_sell_price',
            'min_sell_price' => 'required|numeric|gte:min_sell_price',
            'amount'         => 'required|numeric',
        ];
    }
}
