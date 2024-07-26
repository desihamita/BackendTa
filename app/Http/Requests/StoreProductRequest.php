<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreProductRequest extends FormRequest
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
            'name' => 'string|required|min:3|max:255',
            'slug' => 'string|required|min:3|max:255|unique:products',
            'sku' => 'string|required|min:3|max:255|unique:products',
            'discount_fixed' => 'numeric',
            'discount_percent' => 'numeric',
            'cost' => 'numeric|required',
            'price' => 'numeric|required',
            'status' => 'numeric|required',
            'stock' => 'numeric|required',
            'description' => 'required|max:1000|min:10',
            'photo' => 'required',

            'category_id' => 'numeric',
            'sub_category_id' => 'numeric',
        ];
    }
}