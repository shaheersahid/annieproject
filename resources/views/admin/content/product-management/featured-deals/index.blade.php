@extends('admin.layouts.master')
@section('page-title', 'Featured Comfort Deals')

@section('admin-content')
@php
    $activeCategory = $activeCategory ?? null;
    $tabCounts = $tabCounts ?? collect();
@endphp
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Featured Comfort Deals" :items="[['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'Featured Deals']]" />

        <div class="alert alert-info">
            Each homepage tab has its own product list. Select <strong>Smart Home</strong> or <strong>Office Comfort</strong> below,
            then add/remove products for that tab only. The same product can be added to both tabs if needed.
        </div>

        @if($featuredCategories->isNotEmpty())
            <ul class="nav nav-pills mb-3">
                @foreach($featuredCategories as $category)
                    <li class="nav-item">
                        <a class="nav-link {{ (int) $categoryId === (int) $category->id ? 'active' : '' }}" href="{{ route('admin.featured-deals.index', ['category' => $category->id]) }}">
                            {{ $category->name }}
                            <span class="badge bg-light text-dark ms-1">{{ $tabCounts[$category->id] ?? 0 }}</span>
                        </a>
                    </li>
                @endforeach
            </ul>
        @endif

        <div class="row">
            <div class="col-lg-4">
                <x-admin.card title="Add to {{ $activeCategory?->name ?? 'this tab' }}">
                    <p class="text-muted small">
                        Products added here appear only in the <strong>{{ $activeCategory?->name ?? 'selected' }}</strong> homepage tab
                        unless you also add them to the other tab.
                    </p>
                    <form method="POST" action="{{ route('admin.featured-deals.store') }}">
                        @csrf
                        <input type="hidden" name="category" value="{{ $categoryId }}">
                        <div class="mb-3">
                            <label for="product_id" class="form-label">Product</label>
                            <select class="form-select" id="product_id" name="product_id" required>
                                <option value="">Select a product</option>
                                @foreach($availableProducts as $product)
                                    @php
                                        $otherTabs = collect($product->featured_category_ids ?? [])
                                            ->map(fn ($id) => (int) $id)
                                            ->reject(fn ($id) => $id === (int) $categoryId);
                                        $otherNames = $featuredCategories->whereIn('id', $otherTabs)->pluck('name')->implode(', ');
                                    @endphp
                                    <option value="{{ $product->id }}">
                                        {{ $product->name }}@if($otherNames) (also in {{ $otherNames }})@endif
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary" @disabled($availableProducts->isEmpty() || ! $categoryId)>
                            <i class="fa fa-plus me-1"></i> Add to {{ $activeCategory?->name ?? 'tab' }}
                        </button>
                    </form>
                </x-admin.card>
            </div>

            <div class="col-lg-8">
                <x-admin.card title="{{ $activeCategory?->name ?? 'Featured' }} — selected deals">
                    <x-slot name="headerActions">
                        <a href="{{ route('home') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            View homepage
                        </a>
                    </x-slot>

                    @if($featuredProducts->isEmpty())
                        <p class="text-muted mb-0">No products selected for {{ $activeCategory?->name ?? 'this tab' }} yet.</p>
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
                                            <th>Also in</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($featuredProducts as $index => $product)
                                            @php
                                                $otherTabs = collect($product->featured_category_ids ?? [])
                                                    ->map(fn ($id) => (int) $id)
                                                    ->reject(fn ($id) => $id === (int) $categoryId);
                                                $otherNames = $featuredCategories->whereIn('id', $otherTabs)->pluck('name');
                                            @endphp
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
                                                <td>
                                                    @forelse($otherNames as $name)
                                                        <span class="badge bg-secondary-subtle text-secondary">{{ $name }}</span>
                                                    @empty
                                                        <span class="text-muted">Only this tab</span>
                                                    @endforelse
                                                </td>
                                                <td class="text-end">
                                                    <button type="submit" form="remove-featured-{{ $product->id }}" class="btn btn-sm btn-outline-danger">Remove from tab</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary">Save order</button>
                        </form>
                        @foreach($featuredProducts as $product)
                            <form id="remove-featured-{{ $product->id }}" method="POST" action="{{ route('admin.featured-deals.destroy', $product) }}?category={{ $categoryId }}" onsubmit="return confirm('Remove this product from {{ $activeCategory?->name }} only?');">
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
