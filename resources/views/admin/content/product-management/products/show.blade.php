@extends('admin.layouts.master')
@section('page-title', 'Product Details')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb :title="$product->name" :items="[['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'View']]" />

        <x-admin.card title="Product Details">
            <div class="row g-4">
                <div class="col-md-3">
                    <img src="{{ $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png') }}" alt="{{ $product->name }}" class="img-fluid rounded border">
                </div>
                <div class="col-md-9">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <strong>Name</strong>
                            <div>{{ $product->name }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Type</strong>
                            <div>{{ str($product->product_type)->headline() }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Price</strong>
                            <div>{{ format_price($product->base_price) }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Stock</strong>
                            <div>{{ $product->stock }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Status</strong>
                            <div><span class="badge bg-secondary">{{ ucfirst($product->status) }}</span></div>
                        </div>
                        <div class="col-md-6">
                            <strong>Categories</strong>
                            <div>{{ $product->categories->pluck('name')->implode(', ') ?: '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Brand</strong>
                            <div>{{ $product->brand?->name ?? '-' }}</div>
                        </div>
                        <div class="col-md-3">
                            <strong>Seller</strong>
                            <div>{{ $product->seller?->store_name ?? '-' }}</div>
                        </div>
                        <div class="col-12">
                            <strong>Short Description</strong>
                            <div>{{ $product->short_description ?: '-' }}</div>
                        </div>
                        @if($product->has_variants)
                            <div class="col-12">
                                <strong>Variants</strong>
                                <div class="table-responsive mt-2">
                                    <table class="table table-sm table-bordered align-middle mb-0">
                                        <thead>
                                            <tr>
                                                <th>SKU</th>
                                                <th>Category</th>
                                                <th>Attributes</th>
                                                <th>Price</th>
                                                <th>Stock</th>
                                                <th>Low Stock</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach($product->variants as $variant)
                                                <tr>
                                                    <td>{{ $variant->sku }}</td>
                                                    <td>{{ $variant->category?->name ?? $product->categories->pluck('name')->first() ?? '-' }}</td>
                                                    <td>
                                                        @foreach(($variant->attributes ?? []) as $key => $value)
                                                            <span class="badge bg-light text-dark border">{{ $key }}: {{ $value }}</span>
                                                        @endforeach
                                                    </td>
                                                    <td>{{ format_price($variant->price) }}</td>
                                                    <td>{{ $variant->stock }}</td>
                                                    <td>{{ $variant->low_stock_threshold }}</td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </x-admin.card>
    </div>
</div>
@endsection
