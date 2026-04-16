<?php

declare(strict_types=1);

namespace Erp\Products\Services;

use Erp\Products\Contracts\ProductServiceInterface;
use Erp\Products\Models\Product;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;

class ProductService implements ProductServiceInterface
{
    public function getForSale(int $productId): ?array
    {
        $product = Product::where('id', $productId)->where('is_active', true)->first();

        if (! $product) {
            return null;
        }

        return [
            'id' => $product->id,
            'cost_price' => (float) $product->cost_price,
            'selling_price' => (float) $product->selling_price,
            'tax_percentage' => (float) $product->tax_percentage,
        ];
    }

    public function getPaginatedProducts(int $perPage = 15, ?int $categoryId = null, bool $activeOnly = false): LengthAwarePaginator
    {
        $cacheKey = "products_page_{$perPage}_cat_{$categoryId}_active_{$activeOnly}_page_" . request()->get('page', 1);

        return Cache::remember($cacheKey, 3600, function () use ($perPage, $categoryId, $activeOnly) {
            $query = Product::query()->with('category');
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }
            if ($categoryId) {
                $query->where('category_id', $categoryId);
            }

            return $query->orderBy('name')->paginate($perPage);
        });
    }

    public function createProduct(array $data): Product
    {
        $data['tax_percentage'] = $data['tax_percentage'] ?? 0;
        $data['is_active'] = $data['is_active'] ?? true;
        
        $product = Product::create($data);
        $this->clearCache();

        return $product;
    }

    public function updateProduct(Product $product, array $data): Product
    {
        $product->update($data);
        $this->clearCache();

        return $product->fresh();
    }

    public function deleteProduct(Product $product): void
    {
        $product->delete();
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        Cache::flush();
    }
}
