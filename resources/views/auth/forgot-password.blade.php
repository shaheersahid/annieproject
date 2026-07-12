@extends('layouts.main')

@section('content')
<main class="main">
    <div class="page-header text-center" style="background-image: url('assets/images/page-header-bg.jpg')">
        <div class="container">
            <h1 class="page-title">Forgot Password<span>My Account</span></h1>
        </div>
    </div>

    <x-breadcrumb :items="['Forgot Password' => null]" />

    <div class="page-content">
        <div class="container">
            <div class="login-page bg-image pt-8 pb-8 pt-md-12 pb-md-12 pt-lg-17 pb-lg-17">
                <div class="form-box">
                    <div class="form-tab">
                        <div class="tab-content">
                            <div class="tab-pane fade show active">
                                <h3 class="title title-simple text-left text-uppercase">Forgot Your Password?</h3>
                                <p>Enter your email address and we'll send you a link to reset your password.</p>

                                <form action="{{ route('password.request') }}" method="post">
                                    @csrf
                                    <div class="form-group">
                                        <label for="forgot-email">Email address *</label>
                                        <input type="email" class="form-control @error('email') is-invalid @enderror" id="forgot-email" name="email" value="{{ old('email') }}" required>
                                        @error('email')
                                            <span class="invalid-feedback text-danger d-block" role="alert" style="font-size: 1.3rem; margin-top: 5px;">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>

                                    <div class="form-footer">
                                        <button type="submit" class="btn btn-outline-primary-2">
                                            <span>SEND RESET LINK</span>
                                            <i class="icon-long-arrow-right"></i>
                                        </button>
                                    </div>
                                </form>

                                <div class="text-center mt-3">
                                    <p>Remembered your password? <a href="{{ route('login') }}">Sign In</a></p>
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
