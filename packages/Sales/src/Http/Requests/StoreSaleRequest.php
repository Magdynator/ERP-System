<?php

declare(strict_types=1);

namespace Erp\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'items.*.quantity' => ['required', 'numeric', 'min:0.01'],
            'payments' => ['nullable', 'array'],
            'payments.*.amount' => ['required', 'numeric', 'min:0'],
            'payments.*.method' => ['required', 'string', 'max:50'],
            'payments.*.reference' => ['nullable', 'string', 'max:255'],
            'customer_name' => ['nullable', 'string', 'max:255'],
            'customer_email' => ['nullable', 'email'],
            'currency' => ['nullable', 'string', 'size:3'],
            'branch_id' => ['nullable', 'integer'],
        ];
    }
}
