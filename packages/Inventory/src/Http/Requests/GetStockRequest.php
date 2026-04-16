<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class GetStockRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'product_id' => ['required', 'integer', 'exists:products,id'],
            'warehouse_id' => ['nullable', 'integer', 'exists:warehouses,id'],
        ];
    }
}
