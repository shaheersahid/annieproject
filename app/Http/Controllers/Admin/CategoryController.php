<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getCategoriesDataTable();
        }

        return view('admin.content.product-management.categories.index');
    }

    public function create(): View
    {
        $parents = Category::query()->whereNull('parent_id')->orderBy('name')->get();
        return view('admin.content.product-management.categories.create', compact('parents'));
    }

    public function store(CategoryRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['show_on_home'] = $request->boolean('show_on_home', false);

        $category = Category::create($data);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->images()->create(['path' => $path, 'type' => 'primary', 'order' => 0]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $parents = Category::query()->whereNull('parent_id')->where('id', '!=', $category->id)->orderBy('name')->get();
        return view('admin.content.product-management.categories.edit', compact('category', 'parents'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);
        $data['show_on_home'] = $request->boolean('show_on_home', false);

        $category->update($data);

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('categories', 'public');
            $category->images()->updateOrCreate(['type' => 'primary'], ['path' => $path, 'order' => 0]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category): RedirectResponse
    {
        $category->delete();
        return redirect()->route('admin.categories.index')
            ->with('success', 'Category deleted.');
    }

    public function toggleStatus(Request $request): JsonResponse
    {
        $category = Category::findOrFail($request->integer('id'));
        $type  = $request->input('type', 'status');
        $value = $request->boolean('value');

        if ($type === 'status') {
            $category->update(['is_active' => $value]);
        } elseif ($type === 'homepage') {
            $category->update(['show_on_home' => $value]);
        }

        return response()->json(['success' => true, 'message' => 'Updated.']);
    }

    public function products(Request $request, Category $category): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->categoryProductsTable($category->products()->getQuery());
        }

        return view('admin.content.product-management.categories.products', compact('category'));
    }

    public function quickStore(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = Category::create(['name' => $request->input('name'), 'is_active' => true]);
        return response()->json(['id' => $category->id, 'name' => $category->name]);
    }
}
