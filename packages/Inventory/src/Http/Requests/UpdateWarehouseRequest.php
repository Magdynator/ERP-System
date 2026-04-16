<?php

declare(strict_types=1);

namespace Erp\Inventory\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateWarehouseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $warehouse = $this->route('warehouse');
        $warehouseId = $warehouse instanceof \Erp\Inventory\Models\Warehouse ? $warehouse->id : $warehouse;

        return [
            'name' => ['required', 'string', 'max:255'],
            'code' => ['required', 'string', 'max:255', 'unique:warehouses,code,' . $warehouseId],
            'branch_id' => ['nullable', 'integer'],
            'is_active' => ['boolean'],
        ];
    }
}
