<?php

namespace App\Http\Requests\Newsletter;

use Illuminate\Foundation\Http\FormRequest;

class CreateNewsletterRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Authorization is handled by middleware
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'subject' => 'required|string|max:255',
            'content' => 'required|string',
            'html_content' => 'nullable|string',
            'scheduled_for' => 'nullable|date|after:now',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'subject.required' => 'The newsletter subject is required.',
            'subject.max' => 'The newsletter subject must not exceed 255 characters.',
            'content.required' => 'The newsletter content is required.',
            'scheduled_for.after' => 'The scheduled date and time must be in the future.',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Set the status based on scheduled_for
        $this->merge([
            'status' => $this->input('scheduled_for') ? 'scheduled' : 'draft',
        ]);
    }

    /**
     * Get the validated data with additional processing.
     */
    public function validatedWithStatus($key = null, $default = null)
    {
        $validated = $this->validated();

        // Ensure status is set
        if (!isset($validated['status'])) {
            $validated['status'] = $this->input('scheduled_for') ? 'scheduled' : 'draft';
        }

        // Convert the content to HTML if html_content is not provided
        if (empty($validated['html_content']) && !empty($validated['content'])) {
            $validated['html_content'] = nl2br(e($validated['content']));
        }

        return $key ? ($validated[$key] ?? $default) : $validated;
    }
}