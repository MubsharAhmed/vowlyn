<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Support\ServiceCatalog;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'name' => ['required', 'string', 'min:2', 'max:120'],
            'email' => ['required', 'string', 'email:rfc', 'max:160'],
            'company' => ['nullable', 'string', 'max:160'],
            'service' => ['required', 'string', Rule::in(array_keys(ServiceCatalog::options()))],
            'brief' => ['required', 'string', 'min:20', 'max:4000'],
            'form_source' => ['nullable', 'string', Rule::in(['hero', 'contact'])],

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
            'service.required' => 'Choose the service that best fits your project.',
            'service.in' => 'Choose one of the available Vowlyn services.',
            'website.size' => 'Spam detected.',
        ];
    }
}
