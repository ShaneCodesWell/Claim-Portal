<?php
namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
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
            'branch_id'   => 'required|exists:branches,id',
            'name'        => 'required|string|max:255',
            'code'        => ['required', 'string', 'max:50',
                Rule::unique('departments', 'code')
                    ->ignore($this->route('department'))],
            'description' => 'nullable|string|max:500',
            'is_active'   => 'boolean',
            'department_head_id' => 'nullable|exists:users,id',
        ];
    }
}
