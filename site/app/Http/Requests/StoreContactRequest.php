<?php

declare(strict_types=1);

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

final class StoreContactRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'name'    => ['required', 'string', 'min:2', 'max:120'],
            'email'   => ['required', 'string', 'email:rfc', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'brief'   => ['required', 'string', 'min:20', 'max:4000'],

            // Honeypot — must be empty for non-bots
            'website' => ['nullable', 'size:0'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'brief.min' => 'Tell us a little more about your project (at least 20 characters).',
            'website.size' => 'Spam detected.',
        ];
    }
}
