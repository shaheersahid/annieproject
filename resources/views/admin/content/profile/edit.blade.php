@extends('admin.layouts.master')
@section('page-title', 'My Profile')

@section('admin-content')
<div class="page-content">
    <div class="container-fluid">

        <div class="row">
            <div class="col-12">
                <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                    <h4 class="mb-sm-0 font-size-18">My Profile</h4>
                    <div class="page-title-right">
                        <ol class="breadcrumb m-0">
                            <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                            <li class="breadcrumb-item active">Profile</li>
                        </ol>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            {{-- Sidebar card --}}
            <div class="col-xl-4 col-lg-5">
                <div class="card text-center">
                    <div class="card-body">
                        <div class="avatar-lg mx-auto mb-3">
                            @if ($user->primaryImage)
                                <img src="{{ $user->primary_image_url }}" alt="{{ $user->name }}"
                                    class="rounded-circle img-thumbnail"
                                    style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="avatar-title bg-primary text-white rounded-circle d-flex align-items-center justify-content-center"
                                    style="width: 80px; height: 80px; font-size: 32px;">
                                    {{ strtoupper(substr($user->name ?? 'U', 0, 1)) }}
                                </div>
                            @endif
                        </div>
                        <h4 class="font-size-20">{{ $user->name }}</h4>
                        <p class="text-muted font-size-13">{{ strtoupper($user->getRoleNames()->first() ?? 'Staff') }}</p>

                        <hr>

                        <div class="text-start mt-3">
                            <p class="text-muted mb-2 font-size-13"><strong>Email:</strong> <span class="ms-2">{{ $user->email }}</span></p>
                            <p class="text-muted mb-2 font-size-13"><strong>Phone:</strong> <span class="ms-2">{{ $user->phone ?? 'N/A' }}</span></p>
                            <p class="text-muted mb-2 font-size-13"><strong>Store:</strong> <span class="ms-2">{{ $user->defaultStore?->name ?? 'Central Admin' }}</span></p>
                            <p class="text-muted mb-0 font-size-13">
                                <strong>2FA:</strong>
                                <span class="ms-2">
                                    @if ($twoFactorEnabled)
                                        <span class="badge bg-success">Enabled</span>
                                    @else
                                        <span class="badge bg-secondary">Disabled</span>
                                    @endif
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Main form --}}
            <div class="col-xl-8 col-lg-7">

                {{-- Personal Information --}}
                <div class="card">
                    <div class="card-body">
                        <h4 class="card-title mb-4">Personal Information</h4>

                        <form action="{{ route('admin.profile.update') }}" method="POST" id="ajaxForm" enctype="multipart/form-data">
                            @csrf
                            @method('POST')

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Full Name</label>
                                        <input type="text" name="name"
                                            class="form-control @error('name') is-invalid @enderror"
                                            value="{{ old('name', $user->name) }}" required>
                                        @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Email Address</label>
                                        <input type="email" name="email"
                                            class="form-control @error('email') is-invalid @enderror"
                                            value="{{ old('email', $user->email) }}" required>
                                        @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="text" name="phone"
                                            class="form-control @error('phone') is-invalid @enderror"
                                            value="{{ old('phone', $user->phone) }}"
                                            placeholder="e.g. +44 7700 900000">
                                        @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Profile Photo</label>
                                        <input type="file" name="avatar"
                                            class="form-control @error('avatar') is-invalid @enderror"
                                            accept="image/*">
                                        <small class="text-muted">PNG, JPG, JPEG · max 2 MB</small>
                                        @error('avatar')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <!-- Two factor fields -->
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Two Factor Authentication</label>
                                        <div class="form-check form-switch">
                                            <input class="form-check-input" type="checkbox" id="two_factor_enabled" name="two_factor_enabled" value="1" {{ $twoFactorEnabled ? 'checked' : '' }}>
                                            <label class="form-check-label" for="two_factor_enabled">Enable</label>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Authentication Method</label>

                                        <select name="two_factor_method" id="two_factor_method" class="form-select">
                                            <option value="">Select Method</option>
                                            <option value="email" {{ old('two_factor_method', $user->two_factor_method) === 'email' ? 'selected' : '' }}>Email</option>
                                            <option value="sms" {{ old('two_factor_method', $user->two_factor_method) === 'sms' ? 'selected' : '' }}>SMS</option>
                                        </select>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4">
                            <h4 class="card-title mb-4">Change Password</h4>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">New Password</label>
                                        <input type="password" name="password"
                                            class="form-control @error('password') is-invalid @enderror"
                                            placeholder="Leave blank to keep current"
                                            autocomplete="new-password">
                                        @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Confirm New Password</label>
                                        <input type="password" name="password_confirmation"
                                            class="form-control" autocomplete="new-password">
                                    </div>
                                </div>
                            </div>

                            <div class="mt-2">
                                <button type="submit" class="btn btn-primary w-md">Save Changes</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection


@push('admin-scripts')
<script src="{{ asset('admin/assets/js/custom/index.js') }}"></script>
<script>
$(function() {
    const $phoneInput = $('input[name="phone"]');
    const $methodSelect = $('#two_factor_method');
    const $smsOption = $methodSelect.find('option[value="sms"]');

    function updateSmsOption() {
        const hasPhone = $phoneInput.val().trim().length > 0;
        if (!hasPhone) {
            $smsOption.prop('disabled', true);
            if ($methodSelect.val() === 'sms') {
                $methodSelect.val('email');
            }
            $smsOption.text('SMS (Phone number required)');
        } else {
            $smsOption.prop('disabled', false);
            $smsOption.text('SMS');
        }
    }

    // Initial check
    updateSmsOption();

    // Listen for changes
    $phoneInput.on('input', updateSmsOption);
});
</script>
@endpush