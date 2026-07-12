@extends('admin.layouts.master')

@section('page-title', 'Customer Loyalty Details - ' . $user->name)

@push('admin-styles')
    <link href="{{ asset('admin/assets/libs/datatables.net-bs5/css/dataTables.bootstrap5.min.css') }}" rel="stylesheet"
        type="text/css" />
    <link href="{{ asset('admin/assets/libs/datatables.net-responsive-bs5/css/responsive.bootstrap5.min.css') }}"
        rel="stylesheet" type="text/css" />
    <style>
        .profile-card {
            border: none;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        .stat-card-custom {
            border: none;
            transition: all 0.3s ease;
        }
        .stat-card-custom:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        .icon-shape {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .vip-gold {
            background: linear-gradient(135deg, #FFD700 0%, #B8860B 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 800;
        }
        .table-responsive {
            border-radius: 0 0 0.5rem 0.5rem;
        }
        .card-header-brand {
            border-left: 4px solid var(--bs-primary);
        }
    </style>
@endpush

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <!-- Page Title -->
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-flex align-items-center justify-content-between">
                    <div>
                        <h4 class="mb-1 text-primary">Loyalty & Rewards</h4>
                        <p class="text-muted mb-0 small">Manage customer points and VIP status</p>
                    </div>
                    <div class="page-title-right">
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb m-0 bg-transparent p-0">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item"><a href="{{ route('admin.customers.points.index') }}">Loyalty</a></li>
                                <li class="breadcrumb-item active">{{ $user->name }}</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <!-- Left Column: User Overview -->
            <div class="col-xl-4 col-lg-5">
                <div class="card profile-card mb-4 border-0 shadow-sm">
                    <div class="card-body p-4">
                        <div class="text-center mb-4">
                            <div class="position-relative d-inline-block">
                                <div class="avatar-xl mx-auto mb-3">
                                    <div class="avatar-title bg-primary-subtle text-primary rounded-circle fs-36 fw-bold d-flex justify-content-center align-items-center h-100">
                                        {{ substr($user->name, 0, 1) }}
                                    </div>
                                </div>
                                @if($user->is_vip)
                                    <div class="position-absolute" style="bottom: 10px; right: 0;">
                                        <div class="bg-white rounded-circle p-1 shadow-sm">
                                            <i class="fas fa-crown text-warning fs-20"></i>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            <h5 class="mt-3 mb-1 fw-bold fs-18">{{ $user->name }}</h5>
                            <p class="text-muted mb-0">{{ $user->email }}</p>
                            
                            <div class="mt-3">
                                @if($user->is_vip)
                                    <span class="badge bg-warning-subtle text-warning border border-warning px-3 py-2 rounded-pill fw-bold">
                                        <i class="fas fa-gem me-1"></i> VIP CUSTOMER
                                    </span>
                                @else
                                    <span class="badge bg-light text-muted border px-3 py-2 rounded-pill fw-medium">
                                        STANDARD CUSTOMER
                                    </span>
                                @endif
                            </div>
                        </div>

                        <div class="d-flex border rounded p-3 mb-4 bg-light bg-opacity-50">
                            <div class="flex-grow-1 text-center border-end">
                                <h5 class="mb-0 fw-bold">{{ $user->orders()->count() }}</h5>
                                <small class="text-muted text-uppercase fw-semibold">Orders</small>
                            </div>
                            <div class="flex-grow-1 text-center">
                                <h5 class="mb-0 fw-bold text-primary">{{ format_price(->orders()->sum('total')) }}</h5>
                                <small class="text-muted text-uppercase fw-semibold">Spent</small>
                            </div>
                        </div>

                        <div class="mb-2">
                            <label class="form-label text-muted small fw-bold text-uppercase">Member Since</label>
                            <p class="fw-medium mb-0"><i class="far fa-calendar-alt me-2 text-primary"></i> {{ $user->created_at->format('d M, Y') }}</p>
                        </div>
                        
                        <hr class="my-4 op-1">

                        <div class="d-grid gap-2">
                            <a href="{{ route('admin.customers.points.create', $user) }}" class="btn btn-primary btn-lg shadow-sm">
                                <i class="fas fa-plus-circle me-1"></i> Adjust Points
                            </a>
                            <form action="{{ route('admin.customers.points.toggle-vip', $user) }}" method="POST">
                                @csrf
                                @method('PATCH')
                                <button type="submit" class="btn btn-outline-dark w-100">
                                    @if($user->is_vip)
                                        <i class="fas fa-user-minus me-1"></i> Revoke VIP Status
                                    @else
                                        <i class="fas fa-crown me-1 text-warning"></i> Upgrade to VIP
                                    @endif
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Stats & Transactions -->
            <div class="col-xl-8 col-lg-7">
                <!-- Mini Stats Row -->
                <div class="row mb-4">
                    <div class="col-md-4">
                        <div class="card stat-card-custom border-0 shadow-sm bg-white overflow-hidden">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-primary-subtle text-primary shadow-sm">
                                        <i class="fas fa-coins fs-20"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted mb-0 small fw-bold text-uppercase">Current Points</p>
                                        <h4 class="mb-0 fw-bold">{{ number_format($user->points_balance) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card-custom border-0 shadow-sm bg-white overflow-hidden">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-success-subtle text-success shadow-sm">
                                        <i class="fas fa-arrow-up fs-20"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted mb-0 small fw-bold text-uppercase">Lifetime Earned</p>
                                        <h4 class="mb-0 fw-bold">{{ number_format($pointsEarned) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card stat-card-custom border-0 shadow-sm bg-white overflow-hidden">
                            <div class="card-body p-3">
                                <div class="d-flex align-items-center">
                                    <div class="icon-shape bg-danger-subtle text-danger shadow-sm">
                                        <i class="fas fa-shopping-bag fs-20"></i>
                                    </div>
                                    <div class="ms-3">
                                        <p class="text-muted mb-0 small fw-bold text-uppercase">Total Redeemed</p>
                                        <h4 class="mb-0 fw-bold">{{ number_format($pointsRedeemed) }}</h4>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- History Card -->
                <div class="card border-0 shadow-sm overflow-hidden">
                    <div class="card-header bg-white py-3 card-header-brand">
                        <div class="d-flex align-items-center justify-content-between">
                            <h5 class="card-title mb-0 fw-bold"><i class="fas fa-history me-2 text-muted"></i> Transaction Log</h5>
                            <button class="btn btn-sm btn-light border" onclick="location.reload();">
                                <i class="fas fa-sync-alt"></i>
                            </button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="transactions-table" class="table table-hover table-nowrap align-middle mb-0" style="width:100%">
                                <thead class="bg-light bg-opacity-50 text-muted">
                                    <tr>
                                        <th class="ps-4 border-0 small text-uppercase fw-bold">Date & Time</th>
                                        <th class="border-0 small text-uppercase fw-bold">Activity</th>
                                        <th class="border-0 small text-uppercase fw-bold">Points</th>
                                        <th class="border-0 small text-uppercase fw-bold">Balance After</th>
                                        <th class="border-0 small text-uppercase fw-bold">Reason</th>
                                        <th class="pe-4 border-0 small text-uppercase fw-bold">Order #</th>
                                    </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('admin-scripts')
    <script src="{{ asset('admin/assets/libs/datatables.net/js/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('admin/assets/libs/datatables.net-bs5/js/dataTables.bootstrap5.min.js') }}"></script>
    <script>
        $(document).ready(function() {
            $('#transactions-table').DataTable({
                processing: true,
                serverSide: true,
                ajax: "{{ route('admin.customers.points.show', $user) }}",
                columns: [
                    { data: 'date', name: 'created_at', className: 'ps-4' },
                    { data: 'type_badge', name: 'type' },
                    { data: 'points_display', name: 'points', className: 'fw-semibold' },
                    { data: 'balance_after', name: 'balance_after', className: 'fw-bold text-dark' },
                    { data: 'description', name: 'description', className: 'text-muted' },
                    { data: 'order_link', name: 'order_id', className: 'pe-4' }
                ],
                order: [[0, 'desc']],
                pageLength: 20,
                dom: '<"d-none"f>rt<"d-flex justify-content-between align-items-center p-3 border-top" ip>',
                language: {
                    paginate: {
                        next: '<i class="fas fa-chevron-right"></i>',
                        previous: '<i class="fas fa-chevron-left"></i>'
                    },
                    info: "Showing _START_ to _END_ of _TOTAL_ entries",
                }
            });
        });
    </script>
@endpush
