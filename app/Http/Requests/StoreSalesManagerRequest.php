<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreSalesManagerRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|min:3|max:255',
            'phone' => 'required|numeric',
            'email' => 'required|email',
            'bio' => 'max:1000',
            'photo' => 'required',
            'password' => ['required', Password::min(8)->mixedCase()->numbers()->symbols()->uncompromised()],

            'address' => 'required|min:3|max:255',
            'landmark' => 'max:255',
            'division_id' => 'required|numeric',
            'district_id' => 'required|numeric',
            'sub_district_id' => 'required|numeric',
            'area_id' => 'required|numeric',
        ];
    }
}
