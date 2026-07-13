<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductTagRequest;
use App\Models\ProductTag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class ProductTagController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getProductTagsDataTable();
        }
        return view('admin.content.product-management.tags.index');
    }

    public function create(): View
    {
        return view('admin.content.product-management.tags.create');
    }

    public function store(ProductTagRequest $request): RedirectResponse
    {
        ProductTag::create($request->validated());
        return redirect()->route('admin.attributes.index')->with('success', 'Tag created.');
    }

    public function edit(ProductTag $attribute): View
    {
        return view('admin.content.product-management.tags.edit', compact('attribute'));
    }

    public function update(ProductTagRequest $request, ProductTag $attribute): RedirectResponse
    {
        $attribute->update($request->validated());
        return redirect()->route('admin.attributes.index')->with('success', 'Tag updated.');
    }

    public function destroy(ProductTag $attribute): RedirectResponse
    {
        $attribute->delete();
        return redirect()->route('admin.attributes.index')->with('success', 'Tag deleted.');
    }

    public function toggleActive(ProductTag $attribute): JsonResponse
    {
        $attribute->update(['is_active' => ! $attribute->is_active]);
        return response()->json(['success' => true]);
    }

    public function quickStore(Request $request): JsonResponse
    {
        $request->validate(['name' => 'required|string|max:255']);
        $tag = ProductTag::firstOrCreate(
            ['name' => $request->input('name')],
            ['is_active' => true]
        );
        return response()->json(['id' => $tag->id, 'name' => $tag->name]);
    }
}
