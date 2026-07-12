<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Models\ProductReview;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProductReviewController extends Controller
{
    public function __construct(protected DataTableServiceInterface $dataTable) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getProductReviewsDataTable();
        }
        return view('admin.content.product-management.reviews.index');
    }

    public function update(Request $request, ProductReview $review): JsonResponse
    {
        $request->validate(['status' => 'required|in:approved,rejected,pending']);
        $review->update(['status' => $request->input('status')]);
        return response()->json(['success' => true]);
    }
}
