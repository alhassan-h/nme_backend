<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketInsightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->user_type === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category_id' => ['nullable', 'integer', 'exists:market_insight_categories,id'],
            'featured' => ['nullable', 'boolean'],
            'price_trend' => ['nullable', 'string', 'max:50'],
            'market_volume' => ['nullable', 'string', 'max:50'],
            'investor_confidence' => ['nullable', 'string', 'max:50'],
            'tags' => ['nullable', 'array'],
            'status' => ['nullable', 'in:draft,published'],
        ];
    }

}
