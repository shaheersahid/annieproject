<?php

namespace App\Http\Controllers\Admin;

use App\Contracts\DataTableServiceInterface;
use App\Contracts\VariationGeneratorServiceInterface;
use App\Enums\ProductTypeEnum;
use App\Http\Controllers\Controller;
use App\Http\Requests\ProductRequest;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductAttribute;
use App\Models\ProductTag;
use App\Models\ProductVariant;
use App\Models\Seller;
use App\Models\SizeChart;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\UploadedFile;
use Illuminate\Http\Request;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Throwable;

class ProductController extends Controller
{
    public function __construct(
        protected DataTableServiceInterface $dataTable,
        protected VariationGeneratorServiceInterface $variantGenerator,
    ) {}

    public function index(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getProductsDataTable();
        }
        return view('admin.content.product-management.products.index');
    }

    public function create(): View
    {
        return view('admin.content.product-management.products.create', $this->formData());
    }

    public function store(ProductRequest $request): RedirectResponse
    {
        $product = DB::transaction(function () use ($request) {
            $data = $this->prepareData($request);
            $product = Product::create($data);

            $this->syncCategories($product, $request);
            $this->syncTags($product, $request);
            $this->handleImages($product, $request);
            $this->syncVariants($product, $request);

            return $product;
        });

        if ($request->boolean('is_draft')) {
            return redirect()->route('admin.products.drafts')
                ->with('success', 'Product saved as draft.');
        }

        return redirect()->route('admin.products.index')
            ->with('success', 'Product created successfully.');
    }

    public function show(Product $product): View
    {
        $product->load(['categories', 'brand', 'seller', 'images', 'primaryImage', 'variants']);
        return view('admin.content.product-management.products.show', compact('product'));
    }

    public function edit(Product $product): View
    {
        $product->load(['categories', 'tags', 'images', 'primaryImage', 'variants']);
        return view('admin.content.product-management.products.edit', array_merge(
            $this->formData(),
            ['product' => $product]
        ));
    }

    public function update(ProductRequest $request, Product $product): RedirectResponse
    {
        DB::transaction(function () use ($request, $product) {
            $data = $this->prepareData($request);
            $product->update($data);

            $this->syncCategories($product, $request);
            $this->syncTags($product, $request);
            $this->handleImages($product, $request);
            $this->syncVariants($product, $request);
        });

        return redirect()->route('admin.products.index')
            ->with('success', 'Product updated successfully.');
    }

    public function destroy(Product $product): RedirectResponse
    {
        $product->delete();
        return redirect()->route('admin.products.index')
            ->with('success', 'Product deleted.');
    }

    public function drafts(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getDraftProductsDataTable();
        }
        return view('admin.content.product-management.products.drafts');
    }

    public function stockProducts(Request $request): mixed
    {
        if ($request->ajax()) {
            return $this->dataTable->getStockProductsDataTable();
        }
        return view('admin.content.product-management.products.stock-products');
    }

    public function export(Request $request): mixed
    {
        $format = $request->input('format', 'csv');

        $products = Product::with(['categories', 'brand', 'seller'])->get();
        $headers = ['ID', 'Name', 'SKU', 'Type', 'Platform', 'Price', 'Stock', 'Status'];

        $rows = $products->map(fn ($p) => [
            $p->id, $p->name, $p->sku, $p->product_type,
            $p->affiliate_platform, $p->base_price, $p->stock, $p->status,
        ]);

        $filename = 'products_' . now()->format('Y-m-d') . '.csv';
        $callback = function () use ($headers, $rows) {
            $out = fopen('php://output', 'w');
            fputcsv($out, $headers);
            foreach ($rows as $row) {
                fputcsv($out, $row);
            }
            fclose($out);
        };

        return response()->stream($callback, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    public function reorder(Request $request): JsonResponse
    {
        foreach ($request->input('items', []) as $index => $id) {
            Product::where('id', $id)->update(['sort_order' => $index]);
        }
        return response()->json(['success' => true]);
    }

    public function updateStatus(Request $request): JsonResponse
    {
        $product = Product::findOrFail($request->integer('id'));
        $isActive = (bool) $request->integer('is_active');
        $product->update(['is_active' => $isActive]);

        return response()->json(['success' => true, 'message' => 'Status updated.']);
    }

    public function seoEdit(Product $product): View
    {
        return view('admin.content.product-management.products.edit-meta-fields', compact('product'));
    }

    public function seoUpdate(Request $request, Product $product): RedirectResponse
    {
        $request->validate([
            'meta_title'       => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords'    => 'nullable|string|max:500',
        ]);

        $product->seo()->updateOrCreate(
            ['seoable_id' => $product->id, 'seoable_type' => Product::class],
            ['meta_fields' => $request->only(['meta_title', 'meta_description', 'meta_keywords'])]
        );

        return back()->with('success', 'SEO meta updated.');
    }

    public function variantBuilderData(Product $product): JsonResponse
    {
        $attributes = ProductAttribute::with('categories')
            ->whereHas('categories', function ($q) use ($product) {
                $q->whereIn('categories.id', $product->categories->pluck('id'));
            })
            ->active()
            ->get();

        $attributeValues = $attributes->mapWithKeys(fn ($a) => [$a->id => $a->values ?? []]);

        return response()->json([
            'attributes'      => $attributes,
            'attributeValues' => $attributeValues,
        ]);
    }

    public function generateVariantsPreview(Request $request, Product $product): JsonResponse
    {
        $combinations = $this->variantGenerator->generateCombinations(
            $request->input('attribute_value_map', [])
        );

        return response()->json(['combinations' => $combinations]);
    }

    private function formData(): array
    {
        return [
            'productTypes'   => ProductTypeEnum::cases(),
            'categories'     => Category::active()->with('parent')->orderBy('name')->get(),
            'brands'         => Brand::active()->orderBy('name')->get(),
            'sellers'        => Seller::where('is_active', true)->orderBy('store_name')->get(),
            'sizeCharts'     => SizeChart::orderBy('name')->get(),
            'productTags'    => ProductTag::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function prepareData(ProductRequest $request): array
    {
        return array_merge($request->validated(), [
            'aliexpress_url' => $request->input('aliexpress_url'),
        ]);
    }

    private function syncCategories(Product $product, Request $request): void
    {
        $product->categories()->sync($request->input('category_ids', []));
    }

    private function syncTags(Product $product, Request $request): void
    {
        $product->tags()->sync($request->input('tag_ids', []));
    }

    private function handleImages(Product $product, Request $request): void
    {
        if ($request->hasFile('thumbnail')) {
            $oldThumbnail = $product->images()->where('type', 'primary')->first();
            $path = $this->storePublicFile($request->file('thumbnail'), 'products', 'thumbnail');
            $product->images()->updateOrCreate(
                ['type' => 'primary'],
                ['path' => $path, 'order' => 0]
            );

            if ($oldThumbnail && $oldThumbnail->path !== $path) {
                Storage::disk('public')->delete($oldThumbnail->path);
            }
        }

        if ($request->hasFile('images')) {
            $existing = $product->images()->where('type', 'gallery')->count();
            foreach ($request->file('images') as $index => $file) {
                $path = $this->storePublicFile($file, 'products', 'images');
                $product->images()->create([
                    'path'  => $path,
                    'type'  => 'gallery',
                    'order' => $existing + $index,
                ]);
            }
        }

        if ($request->filled('deleted_images')) {
            $paths = $request->input('deleted_images');
            $product->images()->whereIn('path', $paths)->each(function ($img) {
                Storage::disk('public')->delete($img->path);
                $img->delete();
            });
        }

        if ($request->hasFile('video')) {
            $oldVideo = $product->video_path;
            $videoPath = $this->storePublicFile($request->file('video'), 'products/videos', 'video');
            $product->update(['video_path' => $videoPath]);

            if ($oldVideo && $oldVideo !== $videoPath) {
                Storage::disk('public')->delete($oldVideo);
            }
        }
    }

    private function storePublicFile(UploadedFile $file, string $directory, string $field): string
    {
        try {
            $path = $file->store($directory, 'public');
        } catch (Throwable $exception) {
            report($exception);
            $path = false;
        }

        if (! $path) {
            throw ValidationException::withMessages([
                $field => 'File could not be saved. Check public storage permissions and try again.',
            ]);
        }

        return $path;
    }

    private function syncVariants(Product $product, Request $request): void
    {
        if (! $request->boolean('has_variants')) {
            $product->variants()->delete();
            return;
        }

        $submitted = $request->input('variants', []);
        $existingIds = [];

        foreach ($submitted as $variantData) {
            if (! empty($variantData['id'])) {
                $variant = ProductVariant::find($variantData['id']);
                if ($variant && $variant->product_id === $product->id) {
                    $variant->update($variantData);
                    $existingIds[] = $variant->id;
                    continue;
                }
            }

            $variant = $product->variants()->create($variantData);
            $existingIds[] = $variant->id;
        }

        $product->variants()->whereNotIn('id', $existingIds)->delete();
    }
}
