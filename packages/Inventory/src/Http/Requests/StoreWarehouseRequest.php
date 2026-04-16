<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:warehouses,code'],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ];
    }
}
