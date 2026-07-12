<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryAdjustment;
use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class InventoryAdjustmentController extends Controller
{
    public function __construct(protected ProductService $productService) {}

    public function create(Request $request): View
    {
        $products = Product::active()->orderBy('name')->get();
        return view('admin.content.inventory.add-stock', compact('products'));
    }

    public function store(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'quantity' => 'required|integer|min:1',
            'notes'    => 'nullable|string|max:500',
        ]);

        $this->productService->adjustStock(
            $product,
            $request->integer('quantity'),
            'purchase',
            $request->input('notes'),
            auth()->id(),
        );

        return back()->with('success', 'Stock added successfully.');
    }

    public function storeSelected(Request $request): RedirectResponse
    {
        $request->validate([
            'items'            => 'required|array',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity'   => 'required|integer|min:1',
        ]);

        foreach ($request->input('items') as $item) {
            $product = Product::find($item['product_id']);
            if ($product) {
                $this->productService->adjustStock($product, $item['quantity'], 'purchase', null, auth()->id());
            }
        }

        return redirect()->route('admin.manage-inventory.index')->with('success', 'Stock updated.');
    }
}
