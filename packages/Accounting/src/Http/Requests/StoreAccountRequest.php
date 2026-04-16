<?php

declare(strict_types=1);

namespace Erp\Accounting\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAccountRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:accounts,code'],
            'type' => ['required', 'string', 'in:asset,liability,equity,revenue,expense'],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ];
    }
}
