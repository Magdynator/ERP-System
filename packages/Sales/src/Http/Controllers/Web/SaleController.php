<?php

declare(strict_types=1);

namespace Erp\Sales\Http\Controllers\Web;

use Erp\Sales\Http\Controllers\Controller;
use Erp\Sales\Models\Sale;
use Erp\Sales\Services\SaleService;
use Illuminate\Http\RedirectResponse;
use Erp\Sales\Http\Requests\StoreSaleRequest;
use Erp\Sales\Http\Requests\UpdateSaleRequest;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\Response;
use Barryvdh\DomPDF\Facade\Pdf;

class SaleController extends Controller
{
    public function __construct(
        protected SaleService $saleService
    ) {}

    public function index(): View
    {
        $sales = $this->saleService->getPaginatedSales(15);

        return view('sales.index', compact('sales'));
    }

    public function create(): View
    {
        $warehouses = \Erp\Inventory\Models\Warehouse::where('is_active', true)->orderBy('name')->get();
        $products = \Erp\Products\Models\Product::where('is_active', true)->with('category')->orderBy('name')->get();

        return view('sales.create', compact('warehouses', 'products'));
    }

    public function store(StoreSaleRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        try {
            $this->saleService->createSale(
                warehouseId: (int) $validated['warehouse_id'],
                items: $validated['items'],
                payments: $validated['payments'] ?? [],
                customerName: $validated['customer_name'] ?? null,
                customerEmail: $validated['customer_email'] ?? null,
                currency: $validated['currency'] ?? null,
                branchId: $validated['branch_id'] ?? null
            );
        } catch (\InvalidArgumentException $e) {
            return redirect()->back()->withInput()->with('error', $e->getMessage());
        }

        return redirect()->route('web.sales.index')->with('success', 'Sale created.');
    }

    public function show(Sale $sale): View
    {
        $sale->load(['items', 'payments']);

        return view('sales.show', compact('sale'));
    }

    public function update(UpdateSaleRequest $request, Sale $sale): RedirectResponse
    {
        $validated = $request->validated();
        $this->saleService->updateSale($sale, $validated);

        return redirect()->route('web.sales.show', $sale)->with('success', 'Sale updated.');
    }

    public function invoice(Sale $sale): Response
    {
        $sale->load(['items', 'payments']);
        
        $pdf = Pdf::loadView('sales.invoice', compact('sale'));
        
        return $pdf->download('invoice-' . $sale->sale_number . '.pdf');
    }
}
