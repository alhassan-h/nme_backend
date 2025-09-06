<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'price' => ['required', 'numeric', 'min:0'],
            'quantity' => ['required', 'integer', 'min:1'],
            'unit' => ['required', 'string', 'max:20'],
            'location' => ['required', 'string', 'max:100'],
            'images' => ['required', 'array', 'min:1', 'max:5'],
            'images.*' => ['file', 'image', 'max:5120'],
            'mineral_category_id' => ['nullable', 'integer', 'exists:mineral_categories,id'],
        ];
    }
}
