<?php

namespace App\Http\Requests\Product;

use App\Models\Product;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ProductUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'price' => 'required|numeric|min:0',
            'quantity' => 'required|integer|min:1',
            'unit_id' => 'nullable|integer|exists:units,id',
            'location_id' => 'nullable|integer|exists:locations,id',
            'mineral_category_id' => 'nullable|integer|exists:mineral_categories,id',
            'existing_images' => 'nullable|array',
            'existing_images.*' => 'nullable|string',
            'images' => 'nullable|array',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ];

        // Add status validation with role-based permissions
        if ($this->has('status')) {
            $rules['status'] = ['sometimes', 'string', Rule::in([Product::STATUS_ACTIVE, Product::STATUS_PENDING, Product::STATUS_SOLD])];
        }

        return $rules;
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            if ($this->has('status')) {
                $user = $this->user();
                $newStatus = $this->input('status');
                $product = $this->route('product'); // Assuming the product is bound to the route

                // Check if user owns the product or is admin
                if ($product && $product->seller_id !== $user->id && !$user->isAdmin()) {
                    $validator->errors()->add('status', 'You do not have permission to change this product\'s status.');
                    return;
                }

                // Role-based status change permissions
                if ($newStatus === Product::STATUS_ACTIVE && !$user->isAdmin()) {
                    $validator->errors()->add('status', 'Only administrators can activate products.');
                }

                if ($newStatus === Product::STATUS_SOLD && !$user->isSeller() && !$user->isBoth()) {
                    $validator->errors()->add('status', 'Only sellers can mark products as sold.');
                }

                // Sellers can only change status to sold
                if ($user->isSeller() || $user->isBoth() && !in_array($newStatus, [Product::STATUS_SOLD])) {
                    $validator->errors()->add('status', 'Sellers can only mark products as sold.');
                }
            }
        });
    }
}
