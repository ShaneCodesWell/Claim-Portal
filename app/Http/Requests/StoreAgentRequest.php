<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAgentRequest extends FormRequest
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
            'name'              => 'required|string|max:255',
            'email'             => 'required|email|max:255|unique:agents,email',
            'phone'             => 'nullable|string|max:20',
            'gender'            => 'nullable|in:male,female,other',
            'partner_code'      => 'nullable|string|max:100|unique:agents,partner_code',
            'date_of_birth'     => 'nullable|date',
            'league'            => 'nullable|string|max:100',
            'branch_id'         => 'required|exists:branches,id',
            'user_category'     => 'nullable|string|max:255',
            'sub_user_category' => 'nullable|string|max:255',
        ];
    }
}
