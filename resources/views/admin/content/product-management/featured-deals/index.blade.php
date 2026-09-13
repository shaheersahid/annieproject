@extends('admin.layouts.master')
@section('page-title', 'Featured Comfort Deals')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Featured Comfort Deals" :items="[['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'Featured Deals']]" />

        @if($featuredCategories->isNotEmpty())
            <ul class="nav nav-pills mb-3">
                @foreach($featuredCategories as $category)
                    <li class="nav-item">
                        <a class="nav-link {{ (int) $categoryId === (int) $category->id ? 'active' : '' }}" href="{{ route('admin.featured-deals.index', ['category' => $category->id]) }}">
                            {{ $category->name }}
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="row">
            <div class="col-lg-4">
                <x-admin.card title="Add a Featured Deal">
                    <p class="text-muted small">
                        Choose products for the homepage <strong>Featured Comfort Deals</strong> tabs
                        ({{ $featuredCategories->pluck('name')->implode(' / ') ?: 'home categories' }}).
                        Only selected products show in that tab.
                    </p>
                    <form method="POST" action="{{ route('admin.featured-deals.store') }}">
                        @csrf
                        <input type="hidden" name="category" value="{{ $categoryId }}">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select a product</option>
                                @foreach($availableProducts as $product)
                                    <option value="{{ $product->id }}">{{ $product->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" @disabled($availableProducts->isEmpty())>
                            <i class="fa fa-plus me-1"></i> Add to this tab
                        </button>
                    </form>
                </x-admin.card>
            </div>

            <div class="col-lg-8">
                <x-admin.card title="Selected Featured Deals">
                    <x-slot name="headerActions">
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            View homepage
                        </a>
                    </x-slot>

                    @if($featuredProducts->isEmpty())
                        <p class="text-muted mb-0">No products selected for this tab yet.</p>
                    @else
                        <form method="POST" action="{{ route('admin.featured-deals.reorder') }}">
                            @csrf
                            <input type="hidden" name="category" value="{{ $categoryId }}">
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 90px;">Order</th>
                                            <th>Product</th>
                                            <th>Category</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($featuredProducts as $index => $product)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="order[]" value="{{ $product->id }}">
                                                    <input type="number" class="form-control form-control-sm featured-order-input" min="1" value="{{ $index + 1 }}" aria-label="Display order">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png') }}" alt="{{ product_image_alt($product) }}" class="avatar-sm rounded">
                                                        <div>
                                                            <div class="fw-semibold">{{ $product->name }}</div>
                                                            <small class="text-muted">{{ $product->sku ?: 'No SKU' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>{{ $product->categories->pluck('name')->implode(', ') ?: '-' }}</td>
                                                <td class="text-end">
                                                    <button type="submit" form="remove-featured-{{ $product->id }}" class="btn btn-sm btn-outline-danger">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary">Save order</button>
                        </form>
                        @foreach($featuredProducts as $product)
                            <form id="remove-featured-{{ $product->id }}" method="POST" action="{{ route('admin.featured-deals.destroy', $product) }}?category={{ $categoryId }}" onsubmit="return confirm('Remove this product from Featured Comfort Deals?');">
                                @csrf
                                @method('DELETE')
                            </form>
                        @endforeach
                    @endif
                </x-admin.card>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
<script>
    document.querySelectorAll('.featured-order-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var row = input.closest('tr');
            var tbody = row.parentElement;
            var rows = Array.from(tbody.querySelectorAll('tr'));
            var targetIndex = Math.max(1, parseInt(input.value, 10) || 1) - 1;
            rows.splice(rows.indexOf(row), 1);
            rows.splice(Math.min(targetIndex, rows.length), 0, row);
            rows.forEach(function (item, index) {
                tbody.appendChild(item);
                item.querySelector('.featured-order-input').value = index + 1;
            });
        });
    });
</script>
@endpush
