<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class UserRequest extends FormRequest
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
        $arr = [
            'name'          => 'required|string|max:255',
            'email'         => 'required|unique:users,email|email:dns,filter|max:255',
            'password'      => 'required|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/|confirmed',
        ];

        if ($this->method() === 'PUT') {
            $arr['email'] = [
                'required',
                'email:dns,filter',
                'max:255',
                Rule::unique('users', 'email')->ignore($this->route('user')?->id),
            ];
            $arr['password'] = 'nullable|regex:/^(?=.*[A-Za-z])(?=.*\d)[A-Za-z\d]{8,}$/|confirmed';
        }

        return $arr;
    }
}
