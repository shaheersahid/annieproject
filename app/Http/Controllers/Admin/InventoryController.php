<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;

class InventoryController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getManageInventoryDataTable();
        }
        return view('admin.content.inventory.index');
    }

    public function show(Product $product): View
    {
        $product->load(['inventoryAdjustments', 'categories']);
        return view('admin.content.inventory.show', compact('product'));
    }
}
