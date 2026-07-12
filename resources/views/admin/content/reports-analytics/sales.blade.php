@extends('admin.layouts.master')
@section('page-title', 'Sales Reports')

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet" type="text/css" />
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <x-admin.breadcrumb title="Sales Reports" :items="[['label' => 'Reports & Analytics']]" />

        <div class="row">
            <div class="col-md-3">
                <x-admin.card>
                    <p class="text-muted mb-1">Total Sales</p>
                    <h4 class="mb-0">{{ format_price($totalSales) }}</h4>
                </x-admin.card>
            </div>
            <div class="col-md-3">
                <x-admin.card>
                    <p class="text-muted mb-1">Total Orders</p>
                    <h4 class="mb-0">{{ number_format($totalOrders) }}</h4>
                </x-admin.card>
            </div>
            <div class="col-md-3">
                <x-admin.card>
                    <p class="text-muted mb-1">Average Order</p>
                    <h4 class="mb-0">{{ format_price($averageOrder) }}</h4>
                </x-admin.card>
            </div>
            <div class="col-md-3">
                <x-admin.card>
                    <p class="text-muted mb-1">Refunded</p>
                    <h4 class="mb-0">{{ format_price($refunded) }}</h4>
                </x-admin.card>
            </div>
        </div>

        <x-admin.card title="Accommodation Revenue">
            <canvas id="revenue-chart" height="110"></canvas>
        </x-admin.card>

        <x-admin.card title="Orders">
            <x-admin.table id="sales-orders-table" :headers="['ID', 'Customer', 'Items', 'Amount', 'Tax', 'Status', 'Date', 'Action']">
                @foreach($orders as $order)
                    <tr>
                        <td>#{{ $order->id }}</td>
                        <td>{{ $order->customer?->name ?? '-' }}</td>
                        <td>{{ $order->items->sum('quantity') }}</td>
                        <td>{{ format_price($order->grand_total) }}</td>
                        <td>{{ format_price($order->tax_total) }}</td>
                        <td><span class="badge bg-{{ $order->status == 'publish' ? 'success' : 'danger' }}">{{ ucfirst($order->status) }}</span></td>
                        <td>{{ $order->created_at?->format('d M Y') }}</td>
                        <td>
                            <a href="{{ route('admin.website-orders.show', $order) }}" class="btn btn-sm btn-outline-info" title="View">
                                <i class="fas fa-eye"></i>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </x-admin.table>
        </x-admin.card>
    </div>
</div>
@endsection

@push('admin-scripts')
<script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
<script>
    $(function() {
        $('#sales-orders-table').DataTable({ order: [[6, 'desc']] });

        const labels = @json($chartRows->pluck('date'));
        const values = @json($chartRows->pluck('revenue')->map(fn ($value) => (float) $value));
        const canvas = document.getElementById('revenue-chart');
        const ctx = canvas.getContext('2d');
        const width = canvas.width = canvas.offsetWidth;
        const height = canvas.height = 260;
        const padding = 36;
        const max = Math.max(...values, 1);
        const points = values.map(function(value, index) {
            const x = padding + (index * ((width - padding * 2) / Math.max(values.length - 1, 1)));
            const y = height - padding - ((value / max) * (height - padding * 2));
            return {x, y};
        });

        ctx.clearRect(0, 0, width, height);
        ctx.strokeStyle = '#e5e7eb';
        ctx.lineWidth = 1;
        for (let i = 0; i < 4; i++) {
            const y = padding + i * ((height - padding * 2) / 3);
            ctx.beginPath();
            ctx.moveTo(padding, y);
            ctx.lineTo(width - padding, y);
            ctx.stroke();
        }

        ctx.strokeStyle = '#405189';
        ctx.lineWidth = 3;
        ctx.beginPath();
        points.forEach(function(point, index) {
            if (index === 0) ctx.moveTo(point.x, point.y);
            else ctx.lineTo(point.x, point.y);
        });
        ctx.stroke();

        ctx.fillStyle = '#405189';
        points.forEach(function(point) {
            ctx.beginPath();
            ctx.arc(point.x, point.y, 4, 0, Math.PI * 2);
            ctx.fill();
        });

        ctx.fillStyle = '#6b7280';
        ctx.font = '12px Arial';
        if (labels.length) {
            ctx.fillText(labels[0], padding, height - 10);
            ctx.fillText(labels[labels.length - 1], width - padding - 70, height - 10);
        }
    });
</script>
@endpush
