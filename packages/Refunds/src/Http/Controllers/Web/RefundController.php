<?php

declare(strict_types=1);

namespace Erp\Refunds\Http\Controllers\Web;

use Erp\Refunds\Http\Requests\StoreRefundRequest;
use Erp\Refunds\Models\Refund;
use Erp\Refunds\Services\RefundService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RefundController extends Controller
{
    public function __construct(
        protected RefundService $refundService
    ) {}

    public function index(): View
    {
        $refunds = $this->refundService->getPaginatedRefunds(15);

        return view('refunds.index', compact('refunds'));
    }

    public function create(): View
    {
        $sales = \Erp\Sales\Models\Sale::with(['items.product'])->orderByDesc('sale_date')->limit(100)->get();
        $warehouses = \Erp\Inventory\Models\Warehouse::where('is_active', true)->orderBy('name')->get();

        $salesData = $sales->keyBy('id')->map(fn ($s) => [
            'sale_number' => $s->sale_number,
            'items' => $s->items->map(fn ($i) => [
                'id' => $i->id,
                'product_name' => $i->product?->name ?? 'Product #'.$i->product_id,
                'quantity' => (float) $i->quantity,
                'selling_price' => (float) $i->selling_price,
            ])->values()->all(),
        ])->toArray();

        return view('refunds.create', compact('sales', 'warehouses', 'salesData'));
    }

    public function store(StoreRefundRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Filter out items with quantity 0
        $validated['items'] = array_values(array_filter($validated['items'], function ($item) {
            return (float) ($item['quantity'] ?? 0) > 0;
        }));

        if (empty($validated['items'])) {
            return redirect()->back()->withInput()->with('error', 'At least one item must have quantity greater than 0.');
        }

        try {
            $this->refundService->createRefund(
                saleId: $validated['sale_id'],
                warehouseId: $validated['warehouse_id'],
                items: $validated['items'],
                notes: $validated['notes'] ?? null,
                currency: $validated['currency'] ?? null,
                branchId: $validated['branch_id'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('web.refunds.index')->with('success', 'Refund created.');
    }

    public function show(Refund $refund): View
    {
        $refund->load('items');

        return view('refunds.show', compact('refund'));
    }
}
