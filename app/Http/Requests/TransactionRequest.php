<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class TransactionRequest extends FormRequest
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
            'total_price'            => 'required|numeric|min:0',
            'customer_id'            => 'nullable|uuid|exists:customers,id',
            'items'                  => 'required|array',
            'items.*.inventory_id'   => 'nullable|exists:inventories,id',
            'items.*.qty'            => 'required|numeric|min:0',
            'items.*.price'          => 'required|numeric|min:0',
        ];
    }
}
