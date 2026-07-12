@extends('content.account.layout')

@section('account-content')
    <div class="tab-pane fade show active" id="tab-orders" role="tabpanel">
        <h3 class="title mb-3 text-dark font-weight-bold">Order History</h3>
        
        @if(isset($orders) && count($orders) > 0)
            <div class="table-responsive">
                <table class="table table-wishlist table-mobile" style="min-width: 600px;">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Total</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td class="product-col">
                                    <div class="product">
                                        <h3 class="product-title">
                                            <a href="#" class="font-weight-bold text-dark">#{{ $order->order_number ?? $order->id }}</a>
                                        </h3>
                                    </div>
                                </td>
                                <td class="price-col">{{ $order->created_at ? $order->created_at->format('M d, Y') : 'N/A' }}</td>
                                <td class="stock-col">
                                    <span class="badge badge-pill 
                                        @if(($order->status ?? '') === 'completed') badge-success 
                                        @elseif(($order->status ?? '') === 'pending') badge-warning 
                                        @else badge-info @endif"
                                        style="font-size: 1.1rem; padding: 0.5em 1em;">
                                        {{ ucfirst($order->status ?? 'Processing') }}
                                    </span>
                                </td>
                                <td class="price-col">{{ format_price(->total ?? 0) }}</td>
                                <td class="action-col">
                                    <a href="#" class="btn btn-outline-primary-2 btn-sm btn-round">
                                        <span>Details</span><i class="icon-long-arrow-right"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <!-- Exquisite Empty State Fallback -->
            <div class="text-center py-5 border rounded" style="background-color: #fafafa; border-color: #ebebeb !important;">
                <div class="mb-4">
                    <i class="icon-shopping-cart text-muted" style="font-size: 8rem; opacity: 0.4;"></i>
                </div>
                <h4 class="text-dark font-weight-bold mb-2">No Orders Placed Yet</h4>
                <p class="text-muted mb-4 max-width-xs mx-auto" style="max-width: 400px; line-height: 1.6;">
                    You haven't purchased anything yet. Head over to our shop to browse the latest premium stitched & unstitched designer lawn collections!
                </p>
                <a href="{{ route('product-list') }}" class="btn btn-outline-primary-2 btn-round">
                    <span>START SHOPPING</span><i class="icon-long-arrow-right"></i>
                </a>
            </div>
        @endif
    </div>
@endsection
