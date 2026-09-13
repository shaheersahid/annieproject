@extends('admin.layouts.master')
@section('page-title', 'Latest Deals')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Latest Deals" :items="[['label' => 'Products', 'url' => route('admin.products.index')], ['label' => 'Latest Deals']]" />

        <div class="row">
            <div class="col-lg-4">
                <x-admin.card title="Add a Latest Deal">
                    <p class="text-muted small">Pick any published product. TikTok / Reel picks stay at the top of Latest Deals. Each card links to the product page.</p>
                    <form method="POST" action="{{ route('admin.latest-deals.store') }}">
                        @csrf
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
                            <i class="fa fa-plus me-1"></i> Add to Latest Deals
                        </button>
                    </form>
                </x-admin.card>
            </div>

            <div class="col-lg-8">
                <x-admin.card title="Selected Latest Deals">
                    <x-slot name="headerActions">
                        <a href="{{ route('latest-deals') }}" target="_blank" class="btn btn-outline-secondary btn-sm">
                            View page
                        </a>
                    </x-slot>

                    @if($latestProducts->isEmpty())
                        <p class="text-muted mb-0">No products selected yet. Add products from the left to control this page.</p>
                    @else
                        <form method="POST" action="{{ route('admin.latest-deals.reorder') }}">
                            @csrf
                            <div class="table-responsive">
                                <table class="table align-middle">
                                    <thead>
                                        <tr>
                                            <th style="width: 90px;">Order</th>
                                            <th>Product</th>
                                            <th>Type</th>
                                            <th>Category</th>
                                            <th class="text-end">Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($latestProducts as $index => $product)
                                            <tr>
                                                <td>
                                                    <input type="hidden" name="order[]" value="{{ $product->id }}">
                                                    <input type="number" class="form-control form-control-sm latest-order-input" min="1" value="{{ $index + 1 }}" aria-label="Display order">
                                                </td>
                                                <td>
                                                    <div class="d-flex align-items-center gap-2">
                                                        <img src="{{ $product->primaryImage?->url ?? asset('admin/assets/images/placeholder.png') }}" alt="{{ $product->name }}" class="avatar-sm rounded">
                                                        <div>
                                                            <div class="fw-semibold">{{ $product->name }}</div>
                                                            <small class="text-muted">{{ $product->sku ?: 'No SKU' }}</small>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td>
                                                    @if($product->isTiktokReel())
                                                        <span class="badge bg-dark">TikTok / Reel</span>
                                                    @else
                                                        <span class="badge bg-secondary-subtle text-secondary">Promoted</span>
                                                    @endif
                                                </td>
                                                <td>{{ $product->categories->pluck('name')->implode(', ') ?: '-' }}</td>
                                                <td class="text-end">
                                                    <button type="submit" form="remove-latest-{{ $product->id }}" class="btn btn-sm btn-outline-danger">Remove</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="submit" class="btn btn-primary">Save order</button>
                        </form>
                        @foreach($latestProducts as $product)
                            <form id="remove-latest-{{ $product->id }}" method="POST" action="{{ route('admin.latest-deals.destroy', $product) }}" onsubmit="return confirm('Remove this product from Latest Deals?');">
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
    document.querySelectorAll('.latest-order-input').forEach(function (input) {
        input.addEventListener('change', function () {
            var row = input.closest('tr');
            var tbody = row.parentElement;
            var rows = Array.from(tbody.querySelectorAll('tr'));
            var targetIndex = Math.max(1, parseInt(input.value, 10) || 1) - 1;
            rows.splice(rows.indexOf(row), 1);
            rows.splice(Math.min(targetIndex, rows.length), 0, row);
            rows.forEach(function (item, index) {
                tbody.appendChild(item);
                item.querySelector('.latest-order-input').value = index + 1;
            });
        });
    });
</script>
@endpush
