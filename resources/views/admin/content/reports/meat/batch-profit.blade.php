@extends('admin.layouts.master')
@section('page-title', 'Batch Profitability (Traceability)')

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Batch Profitability" :items="[['label' => 'Reports'], ['label' => 'Meat Analytics'], ['label' => 'Profitability']]" />

            <!-- Filters -->
            <div class="row">
                <div class="col-12">
                    <x-admin.card title="Analysis Scope">
                        <x-slot name="headerActions">
                            <span class="badge bg-soft-info text-info p-2">Tracing 100 Most Recent Batches</span>
                        </x-slot>

                        <form action="{{ route('admin.reports.meat.batch-profit') }}" method="GET" class="row align-items-center g-3">
                            <div class="col-md-4">
                                <label class="form-label text-muted">Select Store</label>
                                <select name="store_id" class="form-select">
                                    <option value="">Global View</option>
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label d-none d-md-block">&nbsp;</label>
                                <button type="submit" class="btn btn-primary w-100">Fetch Analysis</button>
                            </div>
                        </form>
                    </x-admin.card>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <x-admin.card title="Batch Financial Traceability">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-nowrap table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th style="width: 20%">Batch Details</th>
                                        <th style="width: 15%">Status</th>
                                        <th class="text-end">Revenue</th>
                                        <th class="text-end">COGS</th>
                                        <th class="text-end">Gross Profit</th>
                                        <th class="text-end">Margin %</th>
                                        <th class="text-center">Integrity</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($batchProfits as $item)
                                        @php 
                                            $profit = $item->total_revenue - $item->total_cogs;
                                            $margin = $item->total_revenue > 0 ? ($profit / $item->total_revenue) * 100 : 0;
                                            $soldPercent = ($item->total_sold_qty / max($item->initial_quantity, 1)) * 100;
                                        @endphp
                                        <tr>
                                            <td>
                                                <span class="fw-bold d-block text-dark">{{ $item->product_name }}</span>
                                                <span class="text-muted small">Batch: #{{ $item->batch_number }}</span>
                                            </td>
                                            <td>
                                                <div class="small mb-1">Sold: {{ format_qty($item->total_sold_qty ?: $item->total_sold_weight) }} / {{ format_qty($item->initial_quantity) }}</div>
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar bg-info" role="progressbar" style="width: {{ $soldPercent }}%"></div>
                                                </div>
                                            </td>
                                            <td class="text-end fw-medium">{{ format_price(->total_revenue) }}</td>
                                            <td class="text-end text-muted">{{ format_price(->total_cogs) }}</td>
                                            <td class="text-end fw-bold {{ $profit >= 0 ? 'text-success' : 'text-danger' }}">
                                                {{ format_price() }}
                                            </td>
                                            <td class="text-end">
                                                <span class="badge {{ $margin > 30 ? 'bg-soft-success text-success' : ($margin > 15 ? 'bg-soft-warning text-warning' : 'bg-soft-danger text-danger') }}">
                                                    {{ number_format($margin, 1) }}%
                                                </span>
                                            </td>
                                            <td class="text-center">
                                                @if($item->total_sold_qty > 0)
                                                    <i class="ri-checkbox-circle-fill text-success fs-18" title="Traceable Data Available"></i>
                                                @else
                                                    <i class="ri-information-fill text-muted fs-18" title="Stock not yet sold"></i>
                                                @endif
                                            </td>
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
