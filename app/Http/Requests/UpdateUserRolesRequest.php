<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use App\Models\User;
use Spatie\Permission\Models\Role;

class UpdateUserRolesRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        /** @var \App\Models\User $loggedInUser */
        $loggedInUser = Auth::user();

        // Only admins or super admins can update roles.
        return $loggedInUser && $loggedInUser->hasAnyRole(['admin', 'super_admin']);
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array|string>
     */
    public function rules(): array
    {
        return [
            'roles' => 'required|array|min:1',
            'roles.*' => [
                'integer',
                Rule::exists('roles', 'id'),
            ],
        ];
    }

    /**
     * Get the custom validation messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'roles.required' => 'At least one role must be selected.',
            'roles.min' => 'At least one role must be selected.',
            'roles.*.exists' => 'The selected role is invalid.',
        ];
    }
}
