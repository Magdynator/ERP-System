<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Erp\Inventory\Contracts\InventoryServiceInterface;
use Erp\Products\Models\Product;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class ProductController extends Controller implements HasMiddleware
{
    public static function middleware(): array
    {
        return [
            new Middleware('can:manage-products', except: ['index', 'show']),
        ];
    }
    public function __construct(
        protected InventoryServiceInterface $inventory
    ) {}
    public function index(Request $request): View
    {
        $query = Product::with('category');
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->boolean('active_only', false)) {
            $query->where('is_active', true);
        }
        $products = $query->orderBy('name')->paginate(15)->withQueryString();

        return view('products.index', [
            'products' => $products,
            'categories' => \Erp\Products\Models\Category::orderBy('name')->get(),
        ]);
    }

    public function create(): View
    {
        $categories = \Erp\Products\Models\Category::where('is_active', true)->orderBy('name')->get();
        $warehouses = \Erp\Inventory\Models\Warehouse::where('is_active', true)->orderBy('name')->get();

        return view('products.create', compact('categories', 'warehouses'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
            'warehouse_id' => ['required', 'integer', 'exists:warehouses,id'],
            'initial_quantity' => ['required', 'numeric', 'min:0.01'],
        ]);
        $validated['tax_percentage'] = (float) ($validated['tax_percentage'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active', true);
        
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $product = DB::transaction(function () use ($validated, $imagePath) {
            $product = Product::create([
                'name' => $validated['name'],
                'sku' => $validated['sku'],
                'cost_price' => $validated['cost_price'],
                'selling_price' => $validated['selling_price'],
                'tax_percentage' => $validated['tax_percentage'],
                'category_id' => $validated['category_id'] ?? null,
                'is_active' => $validated['is_active'],
                'image_path' => $imagePath,
            ]);

            $warehouseId = (int) $validated['warehouse_id'];
            $initialQty = (float) $validated['initial_quantity'];

            $this->inventory->add(
                $product->id,
                $warehouseId,
                $initialQty,
                'initial_stock',
                $product->id
            );

            return $product;
        });

        $qty = number_format((float) $validated['initial_quantity'], 2);
        return redirect()
            ->route('web.products.index')
            ->with('success', "Product created. Initial stock ({$qty}) added.");
    }

    public function show(Product $product): View
    {
        $product->load('category');
        
        return view('products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $categories = \Erp\Products\Models\Category::where('is_active', true)->orderBy('name')->get();

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, Product $product): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_active' => ['boolean'],
            'image' => ['nullable', 'image', 'mimes:jpeg,png,jpg,gif,webp', 'max:2048'],
        ]);
        $validated['tax_percentage'] = (float) ($validated['tax_percentage'] ?? 0);
        $validated['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
                Storage::disk('public')->delete($product->image_path);
            }
            $validated['image_path'] = $request->file('image')->store('products', 'public');
        }

        $product->update($validated);

        return redirect()->route('web.products.index')->with('success', 'Product updated.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        if ($product->image_path && Storage::disk('public')->exists($product->image_path)) {
            Storage::disk('public')->delete($product->image_path);
        }

        $product->delete();

        return redirect()->route('web.products.index')->with('success', 'Product deleted.');
    }
}
