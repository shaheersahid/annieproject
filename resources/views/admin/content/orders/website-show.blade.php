@extends('admin.layouts.master')
@section('page-title', 'Website Order Details')

@section('admin-content')
    <div class="page-content">
        <div class="container-fluid">
            <x-admin.breadcrumb title="Website Order #{{ $order->id }}" :items="[['label' => 'Website Orders', 'url' => route('admin.website-orders')], ['label' => 'Details']]" />

            <div class="row">
                <div class="col-12">
                    <x-admin.card title="Order Details">
                        <div class="row g-3">
                            <div class="col-md-4">
                                <strong>Order ID:</strong>
                                <div>{{ $order->id }}</div>
                            </div>
                            <div class="col-md-4">
                                <strong>Status:</strong>
                                <div>{{ ucfirst((string) $order->status) }}</div>
                            </div>
                            <div class="col-md-4">
                                <strong>Total:</strong>
                                <div>{{ format_price((float) $order->grand_total) }}</div>
                            </div>
                        </div>
                    </x-admin.card>
                </div>
            </div>
        </div>
    </div>
@endsection
