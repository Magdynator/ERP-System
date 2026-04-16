<?php

declare(strict_types=1);

namespace Erp\Sales\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSaleRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'status' => ['sometimes', 'string', 'max:255'],
            'notes'  => ['nullable', 'string'],
        ];
    }
}
