<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
            'slug' => 'required|string|max:255',
            'sku' => 'required|string|max:255',
            'discount_fixed' => 'numeric',
            'discount_percent' => 'numeric',
            'cost' => 'numeric|required',
            'price' => 'numeric|required',
            'status' => 'required|boolean',
            'stock' => 'required|integer',
            'description' => 'required|max:1000|min:10',
            
            'category_id' => 'required|integer|exists:categories,id',
            'sub_category_id' => 'required|integer|exists:sub_categories,id',
        ];
    }
}