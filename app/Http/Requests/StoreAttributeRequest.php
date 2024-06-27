<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAttributeRequest extends FormRequest
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
            'name' => 'required|string|min:3|max:255|unique:attributes',
            'slug' => 'string|required|min:3|max:255|unique:attributes',
            'sku' => 'string|required|min:3|max:255|unique:attributes',
            'price' => 'numeric|required',
            'status' => 'numeric|required',
            'stock' => 'numeric|required',
            'description' => 'required|max:1000|min:10',

            'brand_id' => 'numeric',
            'country_id' => 'numeric',
            'category_id' => 'numeric',
            'sub_category_id' => 'numeric',
            'supplier_id' => 'numeric',
        ];
    }
}