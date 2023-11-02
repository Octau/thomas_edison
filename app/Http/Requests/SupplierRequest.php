<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class SupplierRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    public function rules(): array
    {
        return [
            'address' => 'required|string|max:255',
            'is_active' => 'boolean',
            'name' => 'required|string|max:255',
            'phone' => 'required|string',
        ];
    }
}
