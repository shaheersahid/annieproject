@extends('content.account.layout')

@section('account-content')
    <div class="tab-pane fade show active" id="tab-dashboard" role="tabpanel">
        <p>
            Hello <span class="font-weight-normal text-dark">{{ auth()->user()->name ?? 'Valued Customer' }}</span> 
        </p>

        <p class="mb-4">
            Welcome to your **Signature By RaiMal's** account portal! From your dashboard, you can effortlessly browse your recent purchases, manage store notifications, and keep your profile and billing details completely up to date.
        </p>

        <!-- Elegant Overview Cards -->
        <div class="row mt-4">
            <!-- Profile Card -->
            <div class="col-md-6 mb-3">
                <div class="card card-dashboard">
                    <div class="card-body">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="icon-user mr-2 text-primary" style="font-size: 2.2rem;"></i> Profile Summary
                        </h3>
                        <p class="mt-2 mb-3">
                            <strong>Name:</strong> {{ auth()->user()->name ?? 'N/A' }}<br>
                            <strong>Email:</strong> {{ auth()->user()->email ?? 'N/A' }}<br>
                            <strong>Joined:</strong> {{ auth()->user()->created_at ? auth()->user()->created_at->format('M d, Y') : 'N/A' }}
                        </p>
                        <a href="{{ route('account.profile') }}" class="btn btn-outline-primary-2 btn-round btn-sm">
                            <span>Edit Profile</span><i class="icon-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Recent Orders Card -->
            <div class="col-md-6 mb-3">
                <div class="card card-dashboard">
                    <div class="card-body">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="icon-shopping-cart mr-2 text-primary" style="font-size: 2.2rem;"></i> Recent Orders
                        </h3>
                        <p class="mt-2 mb-3 text-muted">
                            Check the delivery timeline, shipping details, or fetch your payment invoice for your recent shopping sprees.
                        </p>
                        <a href="{{ route('account.orders') }}" class="btn btn-outline-primary-2 btn-round btn-sm">
                            <span>View Orders</span><i class="icon-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>

            <!-- Notifications Card -->
            <div class="col-md-6 mb-3">
                <div class="card card-dashboard">
                    <div class="card-body">
                        <h3 class="card-title font-weight-bold text-dark">
                            <i class="icon-bell mr-2 text-primary" style="font-size: 2.2rem;"></i> Store Notifications
                        </h3>
                        <p class="mt-2 mb-3 text-muted">
                            Stay updated with our latest designer lawn launches, exclusive discount coupons, and shipment updates.
                        </p>
                        <a href="{{ route('account.notifications') }}" class="btn btn-outline-primary-2 btn-round btn-sm">
                            <span>View Notifications</span><i class="icon-long-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
