<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductAttributeRequest;
use App\Models\Category;
use App\Models\ProductAttribute;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ProductAttributeController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getProductAttributesDataTable();
        }
        return view('admin.content.product-management.product-attributes.index');
    }

    public function create(): View
    {
        $categories = Category::active()->orderBy('name')->get();
        return view('admin.content.product-management.product-attributes.create', compact('categories'));
    }

    public function store(ProductAttributeRequest $request): RedirectResponse
    {
        $attribute = ProductAttribute::create($request->validated());

        if ($request->filled('category_ids')) {
            $attribute->categories()->sync($request->input('category_ids'));
        }

        return redirect()->route('admin.product-attributes.index')->with('success', 'Attribute created.');
    }

    public function edit(ProductAttribute $product_attribute): View
    {
        $categories = Category::active()->orderBy('name')->get();
        $product_attribute->load('categories');
        return view('admin.content.product-management.product-attributes.edit', [
            'attribute' => $product_attribute,
            'categories' => $categories,
        ]);
    }

    public function update(ProductAttributeRequest $request, ProductAttribute $product_attribute): RedirectResponse
    {
        $product_attribute->update($request->validated());

        if ($request->has('category_ids')) {
            $product_attribute->categories()->sync($request->input('category_ids', []));
        }

        return redirect()->route('admin.product-attributes.index')->with('success', 'Attribute updated.');
    }

    public function destroy(ProductAttribute $product_attribute): RedirectResponse
    {
        $product_attribute->delete();
        return redirect()->route('admin.product-attributes.index')->with('success', 'Attribute deleted.');
    }

    public function toggleActive(ProductAttribute $product_attribute): JsonResponse
    {
        $product_attribute->update(['is_active' => ! $product_attribute->is_active]);
        return response()->json(['success' => true]);
    }
}
