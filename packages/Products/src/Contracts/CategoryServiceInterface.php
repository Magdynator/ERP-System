<?php

declare(strict_types=1);

namespace Erp\Products\Contracts;

use Erp\Products\Models\Category;
use Illuminate\Pagination\LengthAwarePaginator;

interface CategoryServiceInterface
{
    public function getPaginatedCategories(int $perPage = 15, bool $activeOnly = false): LengthAwarePaginator;
    public function createCategory(array $data): Category;
    public function updateCategory(Category $category, array $data): Category;
    public function deleteCategory(Category $category): void;
}
