<?php

declare(strict_types=1);

namespace Erp\Products\Http\Controllers;

use Erp\Products\Contracts\CategoryServiceInterface;
use Erp\Products\Http\Requests\StoreCategoryRequest;
use Erp\Products\Http\Requests\UpdateCategoryRequest;
use Erp\Products\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}
    /**
     * @OA\Get(
     *     path="/api/v1/categories",
     *     summary="List all categories",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="active_only",
     *         in="query",
     *         description="Filter by active status",
     *         required=false,
     *         @OA\Schema(type="boolean")
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
     *                         {"id": 1, "name": "Electronics", "slug": "electronics", "products_count": 15, "is_active": true}
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
        $categories = $this->categoryService->getPaginatedCategories(
            $request->integer('per_page', 15),
            $request->boolean('active_only')
        );

        return response()->json(['data' => $categories]);
    }

    /**
     * @OA\Get(
     *     path="/api/v1/categories/{category}",
     *     summary="Get category details",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Category ID",
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
     *                     "name": "Electronics",
     *                     "slug": "electronics",
     *                     "description": "Electronic devices and accessories",
     *                     "is_active": true,
     *                     "products": {
     *                         {"id": 1, "name": "MacBook Pro", "sku": "LAP-MBP-14"}
     *                     }
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function show(Category $category): JsonResponse
    {
        $category->load('products');

        return response()->json(['data' => $category]);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/categories",
     *     summary="Create new category",
     *     tags={"Categories"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"name"},
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="parent_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Category created successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Category created.",
     *                 "data": {
     *                     "id": 2,
     *                     "name": "Furniture",
     *                     "slug": "furniture",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $validated = $request->validated();

        $category = $this->categoryService->createCategory($validated);

        return response()->json(['data' => $category, 'message' => 'Category created.'], 201);
    }

    /**
     * @OA\Put(
     *     path="/api/v1/categories/{category}",
     *     summary="Update existing category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string"),
     *             @OA\Property(property="slug", type="string"),
     *             @OA\Property(property="description", type="string"),
     *             @OA\Property(property="parent_id", type="integer"),
     *             @OA\Property(property="is_active", type="boolean")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Category updated successfully",
     *         @OA\JsonContent(
     *             example={
     *                 "message": "Category updated.",
     *                 "data": {
     *                     "id": 1,
     *                     "name": "Computers & Electronics",
     *                     "slug": "computers-electronics",
     *                     "is_active": true
     *                 }
     *             }
     *         )
     *     ),
     *     @OA\Response(response=404, description="Category not found"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $validated = $request->validated();

        $category = $this->categoryService->updateCategory($category, $validated);

        return response()->json(['data' => $category, 'message' => 'Category updated.']);
    }

    /**
     * @OA\Delete(
     *     path="/api/v1/categories/{category}",
     *     summary="Delete category",
     *     tags={"Categories"},
     *     @OA\Parameter(
     *         name="category",
     *         in="path",
     *         description="Category ID",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=204, description="Category deleted successfully"),
     *     @OA\Response(response=404, description="Category not found")
     * )
     */
    public function destroy(Category $category): JsonResponse
    {
        $this->categoryService->deleteCategory($category);

        return response()->json(['message' => 'Category deleted.'], 204);
    }
}
