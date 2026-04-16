<?php

declare(strict_types=1);

namespace Erp\Core\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules;

class UpdateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $user = $this->route('user');
        $userId = $user instanceof \Erp\Core\Models\User ? $user->id : $user;

        return [
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'role'     => ['required', 'string', 'in:super_admin,admin,user'],
        ];
    }
}
