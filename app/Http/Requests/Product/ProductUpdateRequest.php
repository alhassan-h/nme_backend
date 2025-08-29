<?php

namespace App\Http\Requests\Product;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'title' => ['sometimes', 'string', 'max:255'],
            'description' => ['sometimes', 'string'],
            'category' => ['sometimes', 'string', 'max:100'],
            'price' => ['sometimes', 'numeric', 'min:0'],
            'quantity' => ['sometimes', 'integer', 'min:1'],
            'unit' => ['sometimes', 'string', 'max:20'],
            'location' => ['sometimes', 'string', 'max:100'],
            'images' => ['sometimes', 'array', 'max:5'],
            'images.*' => ['file', 'image', 'max:5120'],
        ];
    }
}
