<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRequest;
use App\Models\Seller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class SellerController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getSellersDataTable();
        }
        return view('admin.content.sellers.index');
    }

    public function create(): View
    {
        return view('admin.content.sellers.create');
    }

    public function store(SellerRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', true);

        if ($request->hasFile('store_logo')) {
            $data['store_logo'] = $request->file('store_logo')->store('sellers', 'public');
        }

        Seller::create($data);

        return redirect()->route('admin.sellers.index')->with('success', 'Seller created.');
    }

    public function edit(Seller $seller): View
    {
        return view('admin.content.sellers.edit', compact('seller'));
    }

    public function update(SellerRequest $request, Seller $seller): RedirectResponse
    {
        $data = $request->validated();
        $data['is_active'] = $request->boolean('is_active', $seller->is_active);

        if ($request->hasFile('store_logo')) {
            $data['store_logo'] = $request->file('store_logo')->store('sellers', 'public');
        }

        $seller->update($data);

        return redirect()->route('admin.sellers.index')->with('success', 'Seller updated.');
    }

    public function destroy(Seller $seller): RedirectResponse
    {
        $seller->delete();
        return redirect()->route('admin.sellers.index')->with('success', 'Seller deleted.');
    }

    public function toggleActive(Seller $seller): JsonResponse
    {
        $seller->update(['is_active' => ! $seller->is_active]);
        return response()->json(['success' => true]);
    }
}
