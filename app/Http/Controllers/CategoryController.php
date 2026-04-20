<?php

namespace App\Http\Controllers;

use App\Enums\TransactionType;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Models\Category;
use App\Services\CategoryService;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function __construct(private CategoryService $categoryService)
    {
    }
    public function index(Request $request)
    {
        $this->authorize('viewAny', Category::class);
        $categories = $this->categoryService->getAllCreatedByUser(auth()->id(), $request->get('type', TransactionType::EXPENSE->value));

        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        $this->authorize('create', Category::class);
        return view('categories.create');
    }

    public function store(StoreCategoryRequest $request)
    {
        $this->categoryService->store($request->validated(), auth()->id());
        return to_route('categories.index')->with('success', 'Categoria criada com sucesso!.');
    }

    public function show(Category $category)
    {
        $this->authorize('view', $category);

        return view('categories.show', compact('category'));
    }

    public function edit(Category $category)
    {
        $this->authorize('update', $category);
        return view('categories.edit', compact('category'));
    }

    public function update(UpdateCategoryRequest $request, Category $category)
    {
        $this->categoryService->update($category, $request->validated());

        return to_route('categories.index')->with('success', 'Categoria atualizada com sucesso!');
    }

    public function destroy(Category $category)
    {
        $this->authorize('delete', $category);

        $this->categoryService->destroy($category);
        return to_route('categories.index')->with('success', 'Categoria removida com sucesso!');
    }
}
