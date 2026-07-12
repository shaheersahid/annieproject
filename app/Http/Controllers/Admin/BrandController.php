<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class BrandController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getBrandsDataTable();
        }
        return view('admin.content.product-management.brands.index');
    }

    public function create(): View
    {
        return view('admin.content.product-management.brands.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:brands,name',
            'slug'      => 'nullable|string|max:255|unique:brands,slug',
            'logo'      => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'slug', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        Brand::create($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand created.');
    }

    public function edit(Brand $brand): View
    {
        return view('admin.content.product-management.brands.edit', compact('brand'));
    }

    public function update(Request $request, Brand $brand): RedirectResponse
    {
        $request->validate([
            'name'      => 'required|string|max:255|unique:brands,name,' . $brand->id,
            'slug'      => 'nullable|string|max:255|unique:brands,slug,' . $brand->id,
            'logo'      => 'nullable|image|max:2048',
            'is_active' => 'nullable|boolean',
        ]);

        $data = $request->only(['name', 'slug', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('logo')) {
            $data['logo'] = $request->file('logo')->store('brands', 'public');
        }

        $brand->update($data);

        return redirect()->route('admin.brands.index')->with('success', 'Brand updated.');
    }

    public function destroy(Brand $brand): RedirectResponse
    {
        $brand->delete();
        return redirect()->route('admin.brands.index')->with('success', 'Brand deleted.');
    }

    public function toggleActive(Brand $brand): JsonResponse
    {
        $brand->update(['is_active' => ! $brand->is_active]);
        return response()->json(['success' => true]);
    }
}
