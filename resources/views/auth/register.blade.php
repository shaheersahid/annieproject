@extends('layouts.main')

@push('page-styles')
<style>
    .form-control:focus + .input-group-append .toggle-password {
        border-color: #c96 !important;
    }
    .form-control.is-invalid ~ .input-group-append .toggle-password {
        border-color: #dc3545 !important;
    }
</style>
@endpush

@section('content')
<main class="main">
    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">Register<span>My Account</span></h1>
        </div>
    </div>

    <x-breadcrumb :items="['Register' => null]" />

    <div class="page-content">
        <div class="container">
            <div class="login-page bg-image pt-8 pb-8 pt-md-12 pb-md-12 pt-lg-17 pb-lg-17" style="background-image: url('assets/images/backgrounds/login-bg.jpg')">
                <div class="form-box">
                    <div class="form-tab">
                        <ul class="nav nav-pills nav-fill" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">Sign In</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link active" href="#">Register</a>
                            </li>
                        </ul>

                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <form action="{{ route('register') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="register-name">Full Name *</label>
                                        <input type="text" class="form-control @error('name') is-invalid @enderror" id="register-name" name="name" value="{{ old('name') }}" required>
                                        @error('name')
                                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.3rem; margin-top: 5px;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-email">Email address *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="register-email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.3rem; margin-top: 5px;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-password">Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" id="register-password" name="password" required style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-light toggle-password" type="button" style="border: 1px solid #ebebeb; border-left: none; padding: 0; background: #fafafa; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; color: #777; box-shadow: none;">
                                                    <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                    <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                                </button>
                                            </div>
                                        </div>
                                        @error('password')
                                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.3rem; margin-top: 5px;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-group">
                                        <label for="register-password-confirm">Confirm Password *</label>
                                        <div class="input-group">
                                            <input type="password" class="form-control" id="register-password-confirm" name="password_confirmation" required style="border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: none;">
                                            <div class="input-group-append">
                                                <button class="btn btn-outline-light toggle-password" type="button" style="border: 1px solid #ebebeb; border-left: none; padding: 0; background: #fafafa; height: 40px; min-width: 40px; display: flex; align-items: center; justify-content: center; color: #777; box-shadow: none;">
                                                    <svg class="eye-open" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: none;"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"></path><circle cx="12" cy="12" r="3"></circle></svg>
                                                    <svg class="eye-closed" xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display: block;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"></path><line x1="1" y1="1" x2="23" y2="23"></line></svg>
                                                </button>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>SIGN UP</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>

                                        <div class="custom-control custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="register-policy" required>
                                            <label class="custom-control-label" for="register-policy">I agree to the <a href="#">privacy policy</a> *</label>
                                        </div>
                                    </div>
                                </form>

                                <div class="form-choice">
                                    <p class="text-center">or sign up with</p>
                                    <a href="{{ route('google.login') }}" class="btn btn-login btn-g w-100">
                                        <i class="icon-google"></i>
                                        Sign Up With Google
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</main>
@endsection

@push('page-scripts')
<script src="{{ asset('assets/js/auth-toggle.js') }}"></script>
@endpush

