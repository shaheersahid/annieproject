@extends('admin.layouts.master')
@section('page-title', 'Inventory Valuation')

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Inventory Valuation" :items="[['label' => 'Reports'], ['label' => 'Meat Analytics'], ['label' => 'Valuation']]" />

            <!-- Filters & Total -->
            <div class="row align-items-stretch">
                <div class="col-md-8">
                    <x-admin.card title="Valuation Filters">
                        <form action="{{ route('admin.reports.meat.valuation') }}" method="GET" class="row align-items-end g-3">
                            <div class="col-md-6">
                                <label class="form-label text-muted">Filter by Store</label>
                                <select name="store_id" class="form-select">
                                    <option value="">All Stores</option>
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <button type="submit" class="btn btn-primary w-100">Refresh Data</button>
                            </div>
                        </form>
                    </x-admin.card>
                </div>
                <div class="col-md-4">
                    <div class="card card-animate border-0 h-100 bg-primary text-white text-center">
                        <div class="card-body d-flex flex-column justify-content-center">
                            <h6 class="text-white-50 text-uppercase fw-semibold mb-3">Total Holding Value</h6>
                            <h1 class="mb-0 fw-bold text-white">{{ format_price() }}</h1>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <x-admin.card title="Live Inventory Audit">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-nowrap table-bordered mb-0" id="valuationTable">
                                <thead class="table-light">
                                    <tr>
                                        <th>Store</th>
                                        <th>Product</th>
                                        <th>Batch #</th>
                                        <th class="text-end">Qty</th>
                                        <th class="text-end">Unit Cost</th>
                                        <th class="text-end">Total Valuation</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($valuation as $item)
                                        <tr>
                                            <td>{{ $item->store_name }}</td>
                                            <td class="fw-medium text-dark">{{ $item->product_name }}</td>
                                            <td><span class="badge bg-soft-primary text-primary">#{{ $item->batch_number }}</span></td>
                                            <td class="text-end">{{ format_qty($item->quantity) }}</td>
                                            <td class="text-end">{{ format_price(->unit_cost) }}</td>
                                            <td class="text-end fw-bold text-primary">{{ format_price(->total_value) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </x-admin.card>
                </div>
            </div>
        </div>
    </div>
@endsection
