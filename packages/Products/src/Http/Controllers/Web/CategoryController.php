<?php

declare(strict_types=1);

namespace Erp\Products\Http\Controllers\Web;

use Erp\Products\Http\Controllers\Controller;
use Erp\Products\Contracts\CategoryServiceInterface;
use Erp\Products\Http\Requests\StoreCategoryRequest;
use Erp\Products\Http\Requests\UpdateCategoryRequest;
use Erp\Products\Models\Category;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

use Illuminate\Routing\Controllers\HasMiddleware;
use Illuminate\Routing\Controllers\Middleware;

class CategoryController extends Controller implements HasMiddleware
{
    public function __construct(
        protected CategoryServiceInterface $categoryService
    ) {}
    public static function middleware(): array
    {
        return [
            new Middleware('can:manage-categories', except: ['index', 'show']),
        ];
    }
    public function index(Request $request): View
    {
        $categories = $this->categoryService->getPaginatedCategories(15, false)->withQueryString();

        return view('categories.index', compact('categories'));
    }

    public function create(): View
    {
        $parents = Category::where('is_active', true)->orderBy('name')->get();

        return view('categories.create', compact('parents'));
    }

    public function store(StoreCategoryRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        $this->categoryService->createCategory($validated);

        return redirect()->route('web.categories.index')->with('success', 'Category created.');
    }

    public function edit(Category $category): View
    {
        $parents = Category::where('is_active', true)->where('id', '!=', $category->id)->orderBy('name')->get();

        return view('categories.edit', compact('category', 'parents'));
    }

    public function update(UpdateCategoryRequest $request, Category $category): RedirectResponse
    {
        $validated = $request->validated();

        $this->categoryService->updateCategory($category, $validated);

        return redirect()->route('web.categories.index')->with('success', 'Category updated.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $this->categoryService->deleteCategory($category);

        return redirect()->route('web.categories.index')->with('success', 'Category deleted.');
    }
}
