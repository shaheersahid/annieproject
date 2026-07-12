@extends('admin.layouts.master')
@section('page-title', 'Inventory Details')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb :title="'Inventory: '.$product->name" :items="[['label' => 'Stock Products', 'url' => route('admin.products.stock-products')], ['label' => 'Order Request']]" />
        <div class="row">
            <div class="col-lg-6">
                <x-admin.card title="Add Stock">
                    <form method="POST" action="{{ route('admin.products.stocks.order-requests.add-stock', $product) }}">
                        @csrf
                        <div class="mb-2">
                            <label class="form-label">Variant (optional)</label>
                            <select class="form-select" name="variant_id">
                                <option value="">Whole Product</option>
                                @foreach($product->variants as $variant)
                                    <option value="{{ $variant->id }}">{{ $variant->sku }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Type</label>
                            <select class="form-select" name="type">
                                <option value="in">Stock In</option>
                                <option value="out">Stock Out</option>
                                <option value="set">Set Exact Stock</option>
                            </select>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Quantity</label>
                            <input type="number" class="form-control" name="quantity" required>
                        </div>
                        <div class="mb-2">
                            <label class="form-label">Note</label>
                            <textarea class="form-control" name="note" rows="2"></textarea>
                        </div>
                        <button class="btn btn-primary">Save Adjustment</button>
                    </form>
                </x-admin.card>
            </div>
            <div class="col-lg-6">
                <x-admin.card title="Adjustment History">
                    <ul class="list-group">
                        @forelse($product->inventoryAdjustments as $adj)
                            <li class="list-group-item d-flex justify-content-between">
                                <span>{{ strtoupper($adj->type) }} {{ $adj->quantity }} @if($adj->variant) ({{ $adj->variant->sku }}) @endif</span>
                                <small>{{ $adj->created_at?->format('d M Y H:i') }}</small>
                            </li>
                        @empty
                            <li class="list-group-item text-muted">No adjustments yet.</li>
                        @endforelse
                    </ul>
                </x-admin.card>
            </div>
        </div>
    </div>
</div>
@endsection
