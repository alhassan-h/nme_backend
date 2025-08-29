<?php

namespace App\Http\Requests\Forum;

use Illuminate\Foundation\Http\FormRequest;

class ForumReplyCreateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'content' => ['required', 'string'],
            'parent_id' => ['nullable', 'integer', 'exists:forum_replies,id'],
        ];
    }
}
