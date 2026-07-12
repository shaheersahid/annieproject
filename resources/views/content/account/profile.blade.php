@extends('content.account.layout')

@section('account-content')
    <div class="tab-pane fade show active" id="tab-profile" role="tabpanel">
        <h3 class="title mb-3 text-dark font-weight-bold">Account Details</h3>

        <!-- Status Alerts -->
        @if(session('status') == 'profile-information-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 1.3rem; border-radius: 4px; background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 1.5rem; margin-bottom: 2rem;">
                <strong>Success!</strong> Your profile details have been successfully updated.
            </div>
        @endif

        @if(session('status') == 'password-updated')
            <div class="alert alert-success alert-dismissible fade show" role="alert" style="font-size: 1.3rem; border-radius: 4px; background-color: #d4edda; border-color: #c3e6cb; color: #155724; padding: 1.5rem; margin-bottom: 2rem;">
                <strong>Success!</strong> Your password has been successfully changed.
            </div>
        @endif

        <!-- Profile Information Form -->
        <form action="{{ route('user-profile-information.update') }}" method="POST" class="mb-5">
            @csrf
            @method('PUT')
            
            <h4 class="font-weight-bold text-dark mb-3" style="font-size: 1.6rem; border-bottom: 1px solid #ebebeb; padding-bottom: 1rem;">Profile Information</h4>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="profile-name">Full Name *</label>
                        <input type="text" class="form-control @error('name', 'updateProfileInformation') is-invalid @enderror" 
                               id="profile-name" name="name" value="{{ old('name', auth()->user()->name) }}" required>
                        @error('name', 'updateProfileInformation')
                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.2rem; margin-top: 5px;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="profile-email">Email Address *</label>
                        <input type="email" class="form-control @error('email', 'updateProfileInformation') is-invalid @enderror" 
                               id="profile-email" name="email" value="{{ old('email', auth()->user()->email) }}" required>
                        @error('email', 'updateProfileInformation')
                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.2rem; margin-top: 5px;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-primary-2 btn-round">
                <span>SAVE CHANGES</span><i class="icon-long-arrow-right"></i>
            </button>
        </form>

        <!-- Change Password Form -->
        <form action="{{ route('user-password.update') }}" method="POST">
            @csrf
            @method('PUT')

            <h4 class="font-weight-bold text-dark mb-3" style="font-size: 1.6rem; border-bottom: 1px solid #ebebeb; padding-bottom: 1rem;">Change Password</h4>

            <div class="form-group">
                <label for="current-password">Current Password *</label>
                <div class="input-group">
                    <input type="password" class="form-control @error('current_password', 'updatePassword') is-invalid @enderror" 
                           id="current-password" name="current_password" required style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                    <div class="input-group-append">
                        <button class="btn btn-outline-light toggle-password" type="button" style="border: 1px solid #ebebeb; border-left: none; padding: 0; background: #fafafa; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; color: #777; box-shadow: none;">
                            <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                            <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                        </button>
                    </div>
                </div>
                @error('current_password', 'updatePassword')
                    <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.2rem; margin-top: 5px;">
                        <strong>{{ $message }}</strong>
                    </span>
                @enderror
            </div>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="new-password">New Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control @error('password', 'updatePassword') is-invalid @enderror" 
                                   id="new-password" name="password" required style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light toggle-password" type="button" style="border: 1px solid #ebebeb; border-left: none; padding: 0; background: #fafafa; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; color: #777; box-shadow: none;">
                                    <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                </button>
                            </div>
                        </div>
                        @error('password', 'updatePassword')
                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.2rem; margin-top: 5px;">
                                <strong>{{ $message }}</strong>
                            </span>
                        @enderror
                    </div>
                </div>

                <div class="col-sm-6">
                    <div class="form-group">
                        <label for="new-password-confirm">Confirm New Password *</label>
                        <div class="input-group">
                            <input type="password" class="form-control" 
                                   id="new-password-confirm" name="password_confirmation" required style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                            <div class="input-group-append">
                                <button class="btn btn-outline-light toggle-password" type="button" style="border: 1px solid #ebebeb; border-left: none; padding: 0; background: #fafafa; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; color: #777; box-shadow: none;">
                                    <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                    <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" class="btn btn-outline-primary-2 btn-round">
                <span>CHANGE PASSWORD</span><i class="icon-long-arrow-right"></i>
            </button>
        </form>
    </div>

    @push('page-styles')
        <style>
            /* Dynamic input focus & error borders */
            .form-control:focus + .input-group-append .toggle-password {
                border-color: #c96 !important;
            }
            .form-control.is-invalid ~ .input-group-append .toggle-password {
                border-color: #dc3545 !important;
            }
        </style>
    @endpush

    @push('page-scripts')
        <script src="{{ asset('assets/js/auth-toggle.js') }}"></script>
    @endpush
@endsection
