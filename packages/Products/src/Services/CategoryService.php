<?php

declare(strict_types=1);

namespace Erp\Products\Services;

use Erp\Products\Contracts\CategoryServiceInterface;
use Erp\Products\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class CategoryService implements CategoryServiceInterface
{
    public function getPaginatedCategories(int $perPage = 15, bool $activeOnly = false): LengthAwarePaginator
    {
        $cacheKey = "categories_page_{$perPage}_active_{$activeOnly}_page_" . request()->get('page', 1);

        return Cache::remember($cacheKey, 3600, function () use ($perPage, $activeOnly) {
            $query = Category::query()->withCount('products');
            
            if ($activeOnly) {
                $query->where('is_active', true);
            }

            return $query->orderBy('name')->paginate($perPage);
        });
    }

    public function createCategory(array $data): Category
    {
        $data['is_active'] = $data['is_active'] ?? true;
        if (empty($data['slug']) && !empty($data['name'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        
        $category = Category::create($data);
        $this->clearCache();

        return $category;
    }

    public function updateCategory(Category $category, array $data): Category
    {
        $category->update($data);
        $this->clearCache();

        return $category->fresh();
    }

    public function deleteCategory(Category $category): void
    {
        $category->delete();
        $this->clearCache();
    }

    protected function clearCache(): void
    {
        // For production, consider using tagged cache: Cache::tags(['categories'])->flush();
        // Since we may be using file driver, flush is broad but achieves the cache invalidation.
        Cache::flush();
    }
}
