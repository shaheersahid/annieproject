@extends('admin.layouts.master')

@section('page-title', 'Adjust Points - ' . $user->name)

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0">Adjust Points: {{ $user->name }}</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.customers.points.index') }}">Loyalty Points</a></li>
                            <li class="breadcrumb-item"><a href="{{ route('admin.customers.points.show', $user) }}">History</a></li>
                            <li class="breadcrumb-item active">Adjust</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-4">
                            <div class="avatar-sm me-3">
                                <div class="avatar-title bg-soft-info text-info rounded-circle fs-18">
                                    <i class="fa fa-coins"></i>
                                </div>
                            </div>
                            <div>
                                <h5 class="mb-0">Manual Points Adjustment</h5>
                                <p class="text-muted mb-0">Current Balanced: <strong>{{ number_format($user->points_balance) }}</strong></p>
                            </div>
                        </div>

                        <form action="{{ route('admin.customers.points.store', $user) }}" method="POST">
                            @csrf
                            
                            <div class="mb-3">
                                <label for="points" class="form-label">Points to Add/Deduct</label>
                                <input type="number" class="form-control @error('points') is-invalid @enderror" id="points" name="points" placeholder="e.g. 500 or -200" required>
                                <small class="text-muted">Use positive numbers to add points, negative to deduct.</small>
                                @error('points')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="mb-4">
                                <label for="reason" class="form-label">Reason for Adjustment</label>
                                <textarea class="form-control @error('reason') is-invalid @enderror" id="reason" name="reason" rows="3" placeholder="e.g. CS Goodwill adjustment, Compensation for delayed delivery" required></textarea>
                                @error('reason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end gap-2">
                                <a href="{{ route('admin.customers.points.show', $user) }}" class="btn btn-light">Cancel</a>
                                <button type="submit" class="btn btn-primary">Apply Adjustment</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
