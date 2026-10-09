<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class StoreEnquiryRequest extends FormRequest
{
    protected $errorBag = 'enquiry';

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'min:2', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:200'],
            'phone' => ['nullable', 'string', 'max:40', 'regex:/^[0-9+()\-\s]{7,40}$/'],
            'service_interest' => ['nullable', 'string', 'max:120'],
            'message' => ['required', 'string', 'min:10', 'max:3000'],
            'website' => ['prohibited'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Please tell us your name.',
            'email.required' => 'Please add an email address so we can reply.',
            'email.email' => 'That email address doesn’t look quite right.',
            'phone.regex' => 'Please use digits, spaces, + or brackets for the phone number.',
            'message.required' => 'Please add a few words about your project.',
            'message.min' => 'Please add a little more detail (at least 10 characters).',
            'website.prohibited' => 'Your message could not be sent. Please try again or contact us directly.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'name' => trim((string) $this->input('name')),
            'email' => trim((string) $this->input('email')),
            'phone' => trim((string) $this->input('phone')) ?: null,
            'service_interest' => trim((string) $this->input('service_interest')) ?: null,
            'message' => trim((string) $this->input('message')),
        ]);
    }
}
