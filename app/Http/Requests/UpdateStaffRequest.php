<?php
namespace App\Http\Requests;

use App\Enums\UserRole;
use Illuminate\Validation\Rule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateStaffRequest extends FormRequest
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
            'name'          => 'required|string|max:255',
            'email'         => ['required', 'email',
                Rule::unique('users', 'email')
                    ->ignore($this->route('staff'))],
            'password'      => 'nullable|string|min:8|confirmed',
            'role'          => 'required|in:' . implode(',', UserRole::staffRoles()),
            'branch_id'     => 'required|exists:branches,id',
            'department_id' => 'required|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
            'is_active'     => 'boolean',
        ];
    }
}
