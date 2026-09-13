<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function __construct(
        protected DataTableServiceInterface $dataTable,
        protected ImageOptimizer $imageOptimizer,
    ) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getCategoriesDataTable();
        }

        return view('admin.content.product-management.categories.index');
    }

    public function create(): View
    {
        $parentCategories = Category::query()->whereNull('parent_id')->orderBy('name')->get();
        return view('admin.content.product-management.categories.create', compact('parentCategories'));
    }

    public function store(CategoryRequest $request): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        unset($data['seo_title'], $data['seo_description'], $data['seo_keywords'], $data['image']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['show_on_home'] = $request->boolean('show_on_home', false);

        $category = Category::create($data);

        $this->syncCategorySeo($category, $request);

        if ($request->hasFile('image')) {
            $path = $this->imageOptimizer->storeResized($request->file('image'), 'categories', 800);
            $category->images()->create(['path' => $path, 'type' => 'primary', 'order' => 0]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully.',
                'redirect' => route('admin.categories.index'),
            ]);
        }

        return redirect()->route('admin.categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function edit(Category $category): View
    {
        $category->load('seo');

        $parentCategories = Category::query()
            ->whereNull('parent_id')
            ->where('id', '!=', $category->id)
            ->orderBy('name')
            ->get();

        return view('admin.content.product-management.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(CategoryRequest $request, Category $category): RedirectResponse|JsonResponse
    {
        $data = $request->validated();
        unset($data['seo_title'], $data['seo_description'], $data['seo_keywords'], $data['image']);
        $data['is_active'] = $request->boolean('is_active', true);
        $data['show_on_home'] = $request->boolean('show_on_home', false);

        $category->update($data);

        $this->syncCategorySeo($category, $request);

        if ($request->hasFile('image')) {
            $path = $this->imageOptimizer->storeResized($request->file('image'), 'categories', 800);
            $category->images()->updateOrCreate(['type' => 'primary'], ['path' => $path, 'order' => 0]);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully.',
                'redirect' => route('admin.categories.index'),
            ]);
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

    private function syncCategorySeo(Category $category, Request $request): void
    {
        if (! $request->exists('seo_title') && ! $request->exists('seo_description') && ! $request->exists('seo_keywords')) {
            return;
        }

        $existing = $category->seo()->first();
        $meta = $existing?->meta_fields ?? [];
        $meta['title'] = trim((string) $request->input('seo_title', $meta['title'] ?? ''));
        $meta['description'] = trim((string) $request->input('seo_description', $meta['description'] ?? ''));
        $meta['keywords'] = trim((string) $request->input('seo_keywords', $meta['keywords'] ?? ''));

        $category->seo()->updateOrCreate(
            ['seoable_id' => $category->id, 'seoable_type' => Category::class],
            [
                'meta_fields' => collect($meta)
                    ->map(fn ($value) => is_string($value) ? trim($value) : $value)
                    ->filter(fn ($value) => $value !== null && $value !== '')
                    ->all(),
            ]
        );
    }
}
