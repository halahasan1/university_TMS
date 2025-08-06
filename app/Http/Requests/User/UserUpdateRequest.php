<?php

namespace App\Http\Requests\User;


use App\Enums\Permissions;
use Illuminate\Foundation\Http\FormRequest;

class UserUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->can('manage-users')?? false;
    }

    public function rules(): array
    {
        return [
            'name'     => ['sometimes', 'string', 'max:255'],
            'email'    => ['sometimes', 'email', 'unique:users,email,' . $this->route('user')],
            'password' => ['nullable', 'min:6', 'confirmed'],
            'role'     => ['sometimes', 'exists:roles,name'],
        ];
    }
}
