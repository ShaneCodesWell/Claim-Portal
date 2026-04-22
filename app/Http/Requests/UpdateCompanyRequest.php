<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCompanyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name'             => 'required|string|max:255',
            'tagline'          => 'nullable|string|max:255',
            'email'            => 'nullable|email',
            'claims_email'     => 'nullable|email',
            'phone_primary'    => 'nullable|string|max:20',
            'phone_secondary'  => 'nullable|string|max:20',
            'phone_tertiary'   => 'nullable|string|max:20',
            'postal_address'   => 'nullable|string|max:255',
            'physical_address' => 'nullable|string|max:255',
            'website'          => 'nullable|url',
            'logo'             => 'nullable|image|max:2048',
        ];
    }
}
