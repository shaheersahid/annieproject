@extends('admin.layouts.master')
@section('page-title', 'Order Details')

@section('admin-content')
    @php
        $backRoute = match ($source ?? null) {
            'website' => route('admin.website-orders'),
            'pos' => route('admin.pos-orders'),
            default => ($order->isPosOrder() ? route('admin.pos-orders') : route('admin.website-orders')),
        };

        $backLabel = match ($source ?? null) {
            'website' => 'Back to Website Orders',
            'pos' => 'Back to POS Sales',
            default => ($order->isPosOrder() ? 'Back to POS Sales' : 'Back to Website Orders'),
        };
    @endphp
    <div class="page-content">
        <div class="container-fluid">

            <div class="row">
                <div class="col-lg-8">
                    <!-- Order Info -->
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <h5 class="card-title mb-0">
                                @if($order->pos_order_number)
                                    <span class="badge bg-dark me-1">POS</span>
                                    Invoice #{{ $order->pos_order_number }}
                                @else
                                    Order #{{ $order->order_number }}
                                @endif
                            </h5>
                            <a href="{{ $backRoute }}" class="btn btn-sm btn-secondary">
                                <i class="fa fa-arrow-left me-1"></i> {{ $backLabel }}
                            </a>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-md-6">
                                    <strong>Date:</strong> {{ $order->created_at->format('M d, Y H:i') }}
                                </div>
                                <div class="col-md-6">
                                    <strong>Customer:</strong>
                                    @if($order->user?->is_pos_default)
                                        <span class="text-muted">Cash Sales / Walk-In</span>
                                    @else
                                        {{ $order->user?->name ?? 'Guest' }}
                                        @if($order->user)
                                            <small class="text-muted">({{ $order->user->email }})</small>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Order Items -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Order Items</h5>
                        </div>
                        <div class="card-body p-0">
                            <table class="table table-bordered mb-0">
                                <thead>
                                    <tr>
                                        <th>Product</th>
                                        <th>Variant</th>
                                        <th class="text-center">Qty</th>
                                        <th class="text-end">Price</th>
                                        <th class="text-end">Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($order->orderItems as $item)
                                        <tr>
                                            <td>{{ $item->product_name }}</td>
                                            <td>{{ $item->variant_name ?: '-' }}</td>
                                            <td class="text-center">{{ format_qty($item->quantity) }}</td>
                                            <td class="text-end">{{ format_price($item->unit_price) }}</td>
                                            <td class="text-end">{{ format_price($item->line_total) }}</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted">No items in this order</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Subtotal:</strong></td>
                                        <td class="text-end">{{ format_price($order->sub_total) }}</td>
                                    </tr>
                                    @if ($order->discount_amount > 0)
                                        <tr>
                                            <td colspan="4" class="text-end text-success"><strong>Discount:</strong></td>
                                            <td class="text-end text-success">
                                                -{{ format_price($order->discount_amount) }}</td>
                                        </tr>
                                    @endif
                                    @if ($order->coupon_discount > 0)
                                        <tr>
                                            <td colspan="4" class="text-end text-success">
                                                <strong>Coupon
                                                    @if ($order->coupon_code)
                                                        <span class="badge bg-success ms-1">{{ $order->coupon_code }}</span>
                                                    @endif
                                                :</strong>
                                            </td>
                                            <td class="text-end text-success">-{{ format_price($order->coupon_discount) }}</td>
                                        </tr>
                                    @endif
                                    <tr>
                                        <td colspan="4" class="text-end">
                                            <strong>Delivery Fee</strong>
                                            @if ($order->delivery_distance)
                                                <small class="text-muted">({{ number_format($order->delivery_distance, 1) }} mi)</small>
                                            @endif
                                            <strong>:</strong>
                                        </td>
                                        <td class="text-end">{{ format_price($order->delivery_fee) }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="4" class="text-end"><strong>Tax:</strong></td>
                                        <td class="text-end">{{ format_price($order->tax_amount) }}</td>
                                    </tr>
                                    <tr class="fw-bold">
                                        <td colspan="4" class="text-end"><strong>Total:</strong></td>
                                        <td class="text-end">{{ format_price($order->grand_total) }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>

                    <!-- Notes -->
                    @if ($order->notes || $order->admin_notes)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Notes</h5>
                            </div>
                            <div class="card-body">
                                @if($order->notes)
                                    <p class="mb-1"><strong>Customer:</strong> {{ $order->notes }}</p>
                                @endif
                                @if($order->admin_notes)
                                    <p class="mb-0"><strong>Admin:</strong> {{ $order->admin_notes }}</p>
                                @endif
                            </div>
                        </div>
                    @endif

                    <!-- POS Cash Details -->
                    @if($order->cash_tendered)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Cash Details</h5>
                            </div>
                            <div class="card-body">
                                <p><strong>Cash Tendered:</strong> {{ format_price($order->cash_tendered) }}</p>
                                <p class="mb-0"><strong>Change Given:</strong> {{ format_price($order->change_given) }}</p>
                            </div>
                        </div>
                    @endif
                </div>

                <div class="col-lg-4">
                    <!-- Status Update -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Update Status</h5>
                        </div>
                        <div class="card-body">
                            <form action="{{ $order->adminRoute('update-status') }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-3">
                                    <label class="form-label">Order Status</label>
                                    <select class="form-select" name="status">
                                        @foreach ($statuses as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ $order->status == $value ? 'selected' : '' }}>{{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Payment Status</label>
                                    <select class="form-select" name="payment_status">
                                        @foreach ($paymentStatuses as $value => $label)
                                            <option value="{{ $value }}"
                                                {{ $order->payment_status == $value ? 'selected' : '' }}>
                                                {{ $label }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="mb-3">
                                    <label class="form-label">Admin Notes</label>
                                    <textarea class="form-control" name="admin_notes" rows="3">{{ $order->admin_notes }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary w-100">
                                    <i class="fa fa-save me-1"></i> Update Order
                                </button>
                            </form>
                        </div>
                    </div>

                    <!-- Payment Info -->
                    <div class="card">
                        <div class="card-header">
                            <h5 class="card-title mb-0">Payment</h5>
                        </div>
                        <div class="card-body">
                            <p><strong>Method:</strong> {{ $order->payment_method_label }}</p>
                            <p class="mb-0">
                                <strong>Status:</strong>
                                <span
                                    class="badge bg-{{ $order->payment_badge }}">{{ ucfirst($order->payment_status) }}</span>
                            </p>
                        </div>
                    </div>

                    <!-- Addresses -->
                    @if ($order->shippingAddress)
                        <div class="card">
                            <div class="card-header">
                                <h5 class="card-title mb-0">Shipping Address</h5>
                            </div>
                            <div class="card-body">
                                <address class="mb-0">
                                    {{ $order->shippingAddress->line1 ?? '' }}<br>
                                    @if (!empty($order->shippingAddress->line2))
                                        {{ $order->shippingAddress->line2 }}<br>
                                    @endif
                                    {{ $order->shippingAddress->city ?? '' }}
                                    {{ $order->shippingAddress->postcode ?? '' }}<br>
                                    {{ $order->shippingAddress->country ?? '' }}
                                </address>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
@endsection
