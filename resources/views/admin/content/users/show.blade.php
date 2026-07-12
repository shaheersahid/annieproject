@extends('admin.layouts.master')

@section('page-title', 'User Profile - ' . $user->name)

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Header -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">User Profile</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.users.index') }}">Users</a></li>
                            <li class="breadcrumb-item active">View Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: User Info -->
            <div class="col-xl-4">
                <div class="card overflow-hidden">
                    <div class="bg-primary-subtle">
                        <div class="row">
                            <div class="col-7">
                                <div class="text-primary p-3">
                                    <h5 class="text-primary">Welcome Back !</h5>
                                    <p>User Profile Information</p>
                                </div>
                            </div>
                            <div class="col-5 align-self-end">
                                <img src="{{ asset('admin/assets/images/profile-img.png') }}" alt="" class="img-fluid">
                            </div>
                        </div>
                    </div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-sm-4">
                                <div class="avatar-md profile-user-wid my-2">
                                    <div class="avatar-title rounded-circle bg-light text-primary text-center py-3 fs-2">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                </div>
                                <h5 class="font-size-15 text-truncate">{{ $user->name }}</h5>
                                <p class="text-muted mb-0 text-truncate">{{ ucfirst($user->getRoleNames()->first() ?? 'Customer') }}</p>
                            </div>

                            <div class="col-sm-8">
                                <div class="pt-4">
                                    <div class="row">
                                        <div class="col-6">
                                            <h5 class="font-size-15">{{ $stats['total_orders'] }}</h5>
                                            <p class="text-muted mb-0">Orders</p>
                                        </div>
                                        <div class="col-6">
                                            <h5 class="font-size-15">{{ format_price($stats['total_spent']) }}</h5>
                                            <p class="text-muted mb-0">Spent</p>
                                        </div>
                                    </div>
                                    <div class="mt-4">
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary waves-effect waves-light btn-sm">Edit Profile <i class="mdi mdi-arrow-right ms-1"></i></a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Personal Information</h4>
                        <div class="table-responsive">
                            <table class="table table-nowrap mb-0">
                                <tbody>
                                    <tr>
                                        <th scope="row">Full Name :</th>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">E-mail :</th>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Mobile :</th>
                                        <td>{{ $user->phone ?? 'Not provided' }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Joined :</th>
                                        <td>{{ $user->created_at->format('d M, Y') }}</td>
                                    </tr>
                                    <tr>
                                        <th scope="row">Verified :</th>
                                        <td>
                                            @if($user->email_verified_at)
                                                <span class="badge bg-success">Yes</span>
                                            @else
                                                <span class="badge bg-warning">No</span>
                                            @endif
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                @if($user->addresses->count() > 0)
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Addresses</h4>
                        @foreach($user->addresses as $address)
                        <div class="mb-3 border-bottom pb-2">
                            <p class="mb-1 fw-bold">{{ $address->label ?? 'Address' }}</p>
                            <p class="text-muted mb-0">
                                {{ $address->address_line_1 }}<br>
                                @if($address->address_line_2) {{ $address->address_line_2 }}<br> @endif
                                {{ $address->city }}@if($address->state), {{ $address->state }}@endif
                                @if($address->postal_code) {{ $address->postal_code }}@endif<br>
                                {{ $address->country }}
                            </p>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Right Column: Stats and Orders -->
            <div class="col-xl-8">
                <div class="row">
                    <div class="col-md-4">
                        <div class="card mini-stats-wid text-center">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Pending Orders</p>
                                        <h4 class="mb-0">{{ $stats['pending_orders'] }}</h4>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded-circle bg-primary-subtle text-primary fs-4">
                                            <i class="mdi mdi-clock-outline"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid text-center">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Completed Orders</p>
                                        <h4 class="mb-0">{{ $stats['completed_orders'] }}</h4>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded-circle bg-success-subtle text-success fs-4">
                                            <i class="mdi mdi-check-circle-outline"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card mini-stats-wid text-center">
                            <div class="card-body">
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-grow-1">
                                        <p class="text-muted fw-medium">Average Order Value</p>
                                        <h4 class="mb-0">{{ format_price($stats['total_orders'] > 0 ? $stats['total_spent'] / $stats['total_orders'] : 0) }}</h4>
                                    </div>
                                    <div class="avatar-sm">
                                        <span class="avatar-title rounded-circle bg-info-subtle text-info fs-4">
                                            <i class="mdi mdi-calculator"></i>
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Order History</h4>
                        <div class="table-responsive">
                            <table class="table align-middle table-nowrap mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th class="align-middle">Order ID</th>
                                        <th class="align-middle">Date</th>
                                        <th class="align-middle">Total</th>
                                        <th class="align-middle">Order Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($user->orders->sortByDesc('created_at')->take(10) as $order)
                                    <tr>
                                        <td class="fw-bold">#{{ $order->id }}</td>
                                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                                        <td>{{ format_price($order->grand_total) }}</td>
                                        <td>
                                            @php
                                                $statusClass = match($order->status) {
                                                    'delivered' => 'bg-success',
                                                    'shipped' => 'bg-info',
                                                    'processing' => 'bg-primary',
                                                    'cancelled' => 'bg-danger',
                                                    default => 'bg-warning',
                                                };
                                            @endphp
                                            <span class="badge {{ $statusClass }}">{{ ucfirst($order->status) }}</span>
                                        </td>
                                    </tr>
                                    @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">No orders found for this user.</td>
                                    </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
