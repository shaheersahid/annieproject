@extends('admin.layouts.master')
@section('page-title', 'Butcher Yield Analysis')

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Butcher Yield Analysis" :items="[['label' => 'Reports'], ['label' => 'Meat Analytics'], ['label' => 'Yield Analysis']]" />

            <!-- Filters -->
            <div class="row">
                <div class="col-12">
                    <x-admin.card title="Report Filters">
                        <form action="{{ route('admin.reports.meat.yield') }}" method="GET" class="row align-items-end g-3">
                            <div class="col-md-3">
                                <label class="form-label">Store</label>
                                <select name="store_id" class="form-select">
                                    <option value="">All Stores</option>
                                    @foreach($stores as $s)
                                        <option value="{{ $s->id }}" {{ request('store_id') == $s->id ? 'selected' : '' }}>{{ $s->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date From</label>
                                <input type="date" name="date_from" class="form-control" value="{{ $dateFrom }}">
                            </div>
                            <div class="col-md-3">
                                <label class="form-label">Date To</label>
                                <input type="date" name="date_to" class="form-control" value="{{ $dateTo }}">
                            </div>
                            <div class="col-md-3">
                                <button type="submit" class="btn btn-primary w-100">Apply Filters</button>
                            </div>
                        </form>
                    </x-admin.card>
                </div>
            </div>

            <!-- Stats Cards -->
            <div class="row">
                <div class="col-md-3">
                    <div class="card card-animate border shadow-none text-center">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">Total Input</h6>
                            <h2 class="mb-0 fw-bold">{{ number_format($stats['total_input_weight'], 2) }} kg</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border shadow-none text-center bg-soft-success">
                        <div class="card-body">
                            <h6 class="text-success text-uppercase fw-semibold mb-3">Usable Output</h6>
                            <h2 class="text-success mb-0 fw-bold">{{ number_format($stats['total_output_weight'], 2) }} kg</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border shadow-none text-center bg-soft-danger">
                        <div class="card-body">
                            <h6 class="text-danger text-uppercase fw-semibold mb-3">Fat/Bone Waste</h6>
                            <h2 class="text-danger mb-0 fw-bold">{{ number_format($stats['total_waste_weight'], 2) }} kg</h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card card-animate border shadow-none text-center">
                        <div class="card-body">
                            <h6 class="text-muted text-uppercase fw-semibold mb-3">Avg. Yield Efficiency</h6>
                            <h2 class="mb-0 fw-bold {{ $stats['avg_yield'] < 70 ? 'text-danger' : 'text-primary' }}">{{ number_format($stats['avg_yield'], 1) }}%</h2>
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-12">
                    <x-admin.card title="Yield Details">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle table-nowrap table-bordered mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Store</th>
                                        <th>Butcher</th>
                                        <th class="text-end">Input</th>
                                        <th class="text-end">Output</th>
                                        <th class="text-end">Waste</th>
                                        <th class="text-center" style="width: 200px;">Efficiency</th>
                                        <th class="text-center">Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($runs as $run)
                                        <tr>
                                            <td>{{ $run->created_at->format('d M, Y H:i') }}</td>
                                            <td>{{ $run->store->name }}</td>
                                            <td>{{ $run->butcher->name }}</td>
                                            <td class="text-end fw-medium">{{ number_format($run->input_weight, 2) }} kg</td>
                                            <td class="text-end text-success fw-medium">{{ number_format($run->output_weight, 2) }} kg</td>
                                            <td class="text-end text-danger fw-medium">{{ number_format($run->waste_weight, 2) }} kg</td>
                                            <td class="text-center">
                                                <div class="progress progress-sm">
                                                    <div class="progress-bar {{ $run->yield_percent < 70 ? 'bg-danger' : ($run->yield_percent < 85 ? 'bg-warning' : 'bg-success') }}" 
                                                        role="progressbar" style="width: {{ $run->yield_percent }}%;" 
                                                        aria-valuenow="{{ $run->yield_percent }}" aria-valuemin="0" aria-valuemax="100">
                                                    </div>
                                                </div>
                                                <div class="mt-1 small fw-bold">
                                                    {{ number_format($run->yield_percent, 1) }}%
                                                </div>
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.processing.runs.show', $run->id) }}" class="btn btn-sm btn-soft-info">
                                                    <i class="ri-eye-line"></i>
                                                </a>
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
