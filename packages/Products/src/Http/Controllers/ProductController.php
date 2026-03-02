<?php

declare(strict_types=1);

namespace Erp\Products\Http\Controllers;

use Erp\Products\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/v1/products",
     *     summary="List all products",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="category_id",
     *         in="query",
     *         description="Filter by category ID",
     *         required=false,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Items per page",
     *         required=false,
     *         @OA\Schema(type="integer", default=15)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "current_page": 1,
     *                     "data": {
     *                         {"id": 1, "name": "MacBook Pro", "sku": "LAP-MBP-14", "cost_price": 1200.00, "selling_price": 1999.00, "category": {"id": 1, "name": "Electronics"}}
     *                     },
     *                     "total": 1
     *                 }
     *             }
     *         )
     *     )
     * )
     */
    public function index(Request $request): JsonResponse
    {
        $query = Product::query()->with('category');
        if ($request->boolean('active_only')) {
            $query->where('is_active', true);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->integer('category_id'));
        }
        $products = $query->orderBy('name')->paginate($request->integer('per_page', 15));

        return response()->json(['data' => $products]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/products/{product}",
     *     summary="Get product details",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             example={
     *                 "data": {
     *                     "id": 1,
     *                     "name": "MacBook Pro",
     *                     "sku": "LAP-MBP-14",
     *                     "cost_price": 1200.00,
     *                     "selling_price": 1999.00,
     *                     "is_active": true,
     *                     "category": {
     *                         "id": 1,
     *                         "name": "Electronics"
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function show(Product $product): JsonResponse
    {
        $product->load('category');

        return response()->json(['data' => $product]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/products",
     *     summary="Create new product",
     *     tags={"Products"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name", "sku", "cost_price", "selling_price"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="sku", type="string"),
     *             @OA\Property(property="cost_price", type="number"),
     *             @OA\Property(property="selling_price", type="number"),
     *             @OA\Property(property="tax_percentage", type="number"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Product created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Product created.",
     *                 "data": {
     *                     "id": 2,
     *                     "name": "Office Chair",
     *                     "sku": "FURN-CHAIR-01",
     *                     "cost_price": 45.50,
     *                     "selling_price": 129.99,
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'sku' => ['required', 'string', 'max:255', 'unique:products,sku'],
            'cost_price' => ['required', 'numeric', 'min:0'],
            'selling_price' => ['required', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_active' => ['boolean'],
        ]);
        $validated['tax_percentage'] = $validated['tax_percentage'] ?? 0;
        $validated['is_active'] = $validated['is_active'] ?? true;

        $product = Product::create($validated);

        return response()->json(['data' => $product, 'message' => 'Product created.'], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/products/{product}",
     *     summary="Update existing product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="sku", type="string"),
     *             @OA\Property(property="cost_price", type="number"),
     *             @OA\Property(property="selling_price", type="number"),
     *             @OA\Property(property="tax_percentage", type="number"),
     *             @OA\Property(property="category_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Product updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Product updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "name": "MacBook Pro M2",
     *                     "sku": "LAP-MBP-14-M2",
     *                     "cost_price": 1200.00,
     *                     "selling_price": 2099.00,
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Product not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(Request $request, Product $product): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'sku' => ['sometimes', 'string', 'max:255', 'unique:products,sku,' . $product->id],
            'cost_price' => ['sometimes', 'numeric', 'min:0'],
            'selling_price' => ['sometimes', 'numeric', 'min:0'],
            'tax_percentage' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'category_id' => ['nullable', 'integer', 'exists:categories,id'],
            'is_active' => ['boolean'],
        ]);

        $product->update($validated);

        return response()->json(['data' => $product->fresh(), 'message' => 'Product updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/products/{product}",
     *     summary="Delete product",
     *     tags={"Products"},
     *     @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="Product ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Product deleted successfully"),
     *     @OA\Response(response=404, description="Product not found")
     * )
     */
    public function destroy(Product $product): JsonResponse
    {
        $product->delete();

        return response()->json(['message' => 'Product deleted.'], 204);
    }
}
