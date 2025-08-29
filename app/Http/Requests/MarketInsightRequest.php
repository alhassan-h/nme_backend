<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class MarketInsightRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->role === 'admin';
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'content' => ['required', 'string'],
            'category' => ['required', 'string', 'max:100'],
            'featured' => ['nullable', 'boolean'],
        ];
    }
}
