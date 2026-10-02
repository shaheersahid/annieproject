<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Http\Controllers\Controller;
use App\Http\Requests\SellerRequest;
use App\Models\Seller;
use App\Services\ImageOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

class SellerController extends Controller
{
    public function __construct(
        protected DataTableServiceInterface $dataTable,
        protected ImageOptimizer $imageOptimizer,
    ) {}

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
            $data['store_logo'] = $this->storeSellerImage($request->file('store_logo'), 800);
        }

        if ($request->hasFile('cover_photo')) {
            $data['cover_photo'] = $this->storeSellerImage($request->file('cover_photo'), 1400);
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
            $oldLogo = $seller->store_logo;
            $data['store_logo'] = $this->storeSellerImage($request->file('store_logo'), 800);
            if ($oldLogo && $oldLogo !== $data['store_logo']) {
                Storage::disk('public')->delete($oldLogo);
            }
        }

        if ($request->hasFile('cover_photo')) {
            $oldCover = $seller->cover_photo;
            $data['cover_photo'] = $this->storeSellerImage($request->file('cover_photo'), 1400);
            if ($oldCover && $oldCover !== $data['cover_photo']) {
                Storage::disk('public')->delete($oldCover);
            }
        }

        $seller->update($data);

        return redirect()->route('admin.sellers.index')->with('success', 'Seller updated.');
    }

    public function destroy(Seller $seller): RedirectResponse
    {
        if ($seller->store_logo) {
            Storage::disk('public')->delete($seller->store_logo);
        }
        if ($seller->cover_photo) {
            Storage::disk('public')->delete($seller->cover_photo);
        }

        $seller->delete();
        return redirect()->route('admin.sellers.index')->with('success', 'Seller deleted.');
    }

    private function storeSellerImage(UploadedFile $file, int $maxWidth): string
    {
        return $this->imageOptimizer->storeResized(
            $file,
            'sellers',
            $maxWidth,
            82,
            ImageOptimizer::MAX_BYTES,
            $file->getClientOriginalName()
        );
    }

    public function toggleActive(Seller $seller): JsonResponse
    {
        $seller->update(['is_active' => ! $seller->is_active]);
        return response()->json(['success' => true]);
    }
}
